<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of InDefero, an open source project management application.
# Copyright (C) 2008 Céondo Ltd and contributors.
#
# InDefero is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# InDefero is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

Gatuf::loadFunction('Gatuf_HTTP_URL_urlForView');

/**
 * Confirmation of the form.
 *
 */
class Registro_Form_RegisterConfirmation extends Gatuf_Form {
	public $_user = null;

	public function initFields($extra=array()) {
		$this->_user = $extra['user'];

		$this->fields['key'] = new Gatuf_Form_Field_Varchar(
			array(
				'required' => true,
				'label' => 'Tu clave de confirmación',
				'initial' => $extra['key'],
				'widget' => 'Gatuf_Form_Widget_HiddenInput',
				'widget_attrs' => array(
					'readonly' => 'readonly',
				),
		));
		$this->fields['first_name'] = new Gatuf_Form_Field_Varchar(
			array('required' => false,
				'label' => 'Nombre',
				'initial' => '',
				'widget_attrs' => array(
					'maxlength' => 50,
					'size' => 15,
				),
		));
		$this->fields['last_name'] = new Gatuf_Form_Field_Varchar(
			array(
				'required' => true,
				'label' => 'Apellidos',
				'initial' => '',
				'widget_attrs' => array(
					'maxlength' => 50,
					'size' => 15,
				),
		));
		
		$this->fields['password'] = new Gatuf_Form_Field_Varchar(
			array(
				'required' => true,
				'label' => 'Tu contraseña',
				'initial' => '',
				'widget' => 'Gatuf_Form_Widget_PasswordInput',
				'help_text' => 'Tu contraseña debe ser difícil de encontrar para otras personas, pero fácil de recordar para tí.',
				'widget_attrs' => array (
					'maxlength' => 50,
					'size' => 15,
				),
		));
		
		$this->fields['password2'] = new Gatuf_Form_Field_Varchar(
			array(
				'required' => true,
				'label' => 'Confirma tu contraseña',
				'initial' => '',
				'widget' => 'Gatuf_Form_Widget_PasswordInput',
				'widget_attrs' => array(
					'maxlength' => 50,
					'size' => 15,
				),
		));
		
		$this->fields['udg'] = new Gatuf_Form_Field_Boolean (
		    array (
		        'required' => false,
		        'label' => 'Estudiante U de G',
		        'initial' => true,
		));
		
		$this->fields['codigo'] = new Gatuf_Form_Field_Varchar (
		    array (
		        'required' => false,
		        'label' => 'Código U de G',
		        'initial' => '',
		));
		
		$this->fields['escuela'] = new Gatuf_Form_Field_Varchar (
		    array (
		        'required' => false,
		        'label' => 'Escuela de procedencia',
		        'initial' => '',
		));
		
		$choices = array ();
		
		$choices ['Otra carrera'] = array ();
		$c = new Calif_Carrera ('A1');
		$choices ['Otra carrera'][$c->descripcion] = $c->clave;
		
		$sql = new Gatuf_SQL ('clave LIKE %s', 'A%');
		$choices ['Secundaria y Media Superior'] = array ();
		foreach (Gatuf::factory ('Calif_Carrera')->getList (array ('filter' => $sql->gen ())) as $carrera) {
		    if ($carrera->clave == 'A1') continue;
		    $choices ['Secundaria y Media Superior'][$carrera->descripcion] = $carrera->clave;
		}
		
		$sql = new Gatuf_SQL ('clave NOT LIKE %s AND clave NOT LIKE %s', array ('A%', 'Z%'));
		$choices ['Centro Universitario de Ciencias Exactas e Ingenierias'] = array ();
		foreach (Gatuf::factory ('Calif_Carrera')->getList (array ('filter' => $sql->gen ())) as $carrera) {
		    $choices ['Centro Universitario de Ciencias Exactas e Ingenierias'][$carrera->descripcion] = $carrera->clave;
		}
		
		$sql = new Gatuf_SQL ('clave LIKE %s', 'Z%');
		$choices ['Otras'] = array ();
		foreach (Gatuf::factory ('Calif_Carrera')->getList (array ('filter' => $sql->gen ())) as $carrera) {
		    $choices ['Otras'][$carrera->descripcion] = $carrera->clave;
		}
		
		$this->fields['carrera'] = new Gatuf_Form_Field_Varchar (
		    array (
		        'required' => true,
		        'label' => 'Carrera',
		        'initial' => '',
		        'widget' => 'Gatuf_Form_Widget_SelectInput',
		        'widget_attrs' => array (
		            'choices' => $choices,
		        ),
		));
		
		$this->fields['desc_carrera'] = new Gatuf_Form_Field_Varchar (
		    array (
		        'required' => false,
		        'label' => 'Nombre de la carrera',
		        'initial' => '',
		        'widget_attrs' => array (
		            'size' => 20,
		        ),
		));
	}

	/**
	 * Just a simple control.
	 */
	public function clean_key() {
		$this->cleaned_data['key'] = trim($this->cleaned_data['key']);
		$error = 'La clave de confirmación no es válida. Deberías copiarla y pegarla directamente desde tu correo.';
		if (false === ($email_id = Registro_Form_RegisterInputKey::checkKeyHash($this->cleaned_data['key']))) {
			throw new Gatuf_Form_Invalid($error);
		}
		$guser = new Registro_User();
		$sql = new Gatuf_SQL('email=%s AND id=%s', $email_id);
		$users = $guser->getList(array('filter' => $sql->gen()));
		if ($users->count() != 1) {
			throw new Gatuf_Form_Invalid($error);
		}
		if ($users[0]->active) {
			throw new Gatuf_Form_Invalid('Esta cuenta ya está activada. FIXME: Deberías probar a recuperar tu contraseña.');
		}
		$this->_user_id = $email_id[1];
		return $this->cleaned_data['key'];
	}

	/**
	 * Check the passwords.
	 */
	public function clean() {
		if ($this->cleaned_data['password'] != $this->cleaned_data['password2']) {
			throw new Gatuf_Form_Invalid('Las dos contraseñas deben coincidir.');
		}
		
		if ($this->cleaned_data['udg']) {
		    if (!preg_match ('/^\w\d{8}$/', $this->cleaned_data['codigo'])) {
		        throw new Gatuf_Form_Invalid ('Si eres estudiante de la Universidad de Guadalajara debes proporcionar tu código');
		    }
		} else {
		    if (!isset ($this->cleaned_data ['escuela']) || $this->cleaned_data ['escuela'] == '') {
		        throw new Gatuf_Form_Invalid ('Debes indicar tu escuela de procedencia');
		    }
		}
		
		if ($this->cleaned_data['carrera'] == 'A3') {
		    if (!isset ($this->cleaned_data ['desc_carrera']) || $this->cleaned_data ['desc_carrera'] == '') {
		        throw new Gatuf_Form_Invalid ('Debes indicar tu carrera');
		    }
		}
		return $this->cleaned_data;
	}

	/**
	 * Save the model in the database.
	 *
	 * @param bool Commit in the database or not. If not, the object
	 *			 is returned but not saved in the database.
	 * @return Object Model with data set from the form.
	 */
	function save($commit=true) {
		if (!$this->isValid()) {
			throw new Exception('Cannot save an invalid form.');
		}
		$this->_user->setFromFormData($this->cleaned_data);
		$this->_user->active = true;
		$this->_user->administrator = false;
		$this->_user->staff = false;
		
		if ($this->cleaned_data['carrera'] == 'A1') {
		    $carrera = new Calif_Carrera ();
		    $carrera->clave = 'Z'. (Calif_Carrera::maxZID () + 1);
		    $carrera->descripcion = $this->cleaned_data ['desc_carrera'];
		    
		    $carrera->create ();
		} else {
		    $carrera = new Calif_Carrera ($this->cleaned_data ['carrera']);
		}
		
		$this->_user->carrera = $carrera;
		
		if ($this->cleaned_data['udg']) {
		    $this->_user->codigo = $this->cleaned_data ['codigo'];
		    $this->_user->escuela = 'Universidad de Guadalajara';
		} else {
		    $this->_user->codigo = null;
		    $this->_user->escuela = $this->cleaned_data ['escuela'];
		}
		
		if ($commit) {
			$this->_user->update();
			/**
			 * [signal]
			 *
			 * Pluf_User::passwordUpdated
			 *
			 * [sender]
			 *
			 * IDF_Form_RegisterConfirmation
			 *
			 * [description]
			 *
			 * This signal is sent when the user updated his
			 * password from his account page.
			 *
			 * [parameters]
			 *
			 * array('user' => $user)
			 *
			 */
			$params = array('user' => $this->_user);
			Gatuf_Signal::send('Gatuf_User::passwordUpdated',
							  'Registro_Form_RegisterConfirmation', $params);
		}
		return $this->_user;
	}
}
