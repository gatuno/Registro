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

/**
 * Create a new user account.
 *
 */
class Registro_Form_Register extends Gatuf_Form {
	protected $request;

	public function initFields($extra=array()) {
		$this->request = $extra['request'];
		$login = '';
		if (isset($extra['initial']['login'])) {
			$login = $extra['initial']['login'];
		}
		$this->fields['login'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Tu login',
				'max_length' => 15,
				'min_length' => 3,
				'initial' => $login,
				'help_text' => 'Tu login debe ser entre 3 y 15 caracteres de largo y contener solamente dígitos y letras.',
				'widget_attrs' => array(
					'maxlength' => 15,
					'size' => 10,
				),
		));
		$this->fields['email'] = new Gatuf_Form_Field_Email (
			array (
				'required' => true,
				'label' => 'Tu correo electrónico',
				'initial' => '',
				'help_text' => '',
		));
		
		$this->fields['terms'] = new Gatuf_Form_Field_Boolean (
			array (
				'required' => true,
				'label' => 'Acepto los términos y condiciones de los cursos de invierno.',
				'initial' => '',
		));
	}

	/**
	 * Validate the interconnection in the form.
	 */
	public function clean_login() {
		$this->cleaned_data['login'] = mb_strtolower(trim($this->cleaned_data['login']));
		if (preg_match('/[^A-Za-z0-9]/', $this->cleaned_data['login'])) {
			throw new Gatuf_Form_Invalid(sprintf('El login "%s" solo puede contener letras y dígitos.', $this->cleaned_data['login']));
		}
		$guser = new Registro_User();
		$sql = new Gatuf_SQL('login=%s', $this->cleaned_data['login']);
		if ($guser->getCount(array('filter' => $sql->gen())) > 0) {
			throw new Gatuf_Form_Invalid(sprintf('El login "%s" ya está siendo utilizado, por favor encuentra otro diferente.', $this->cleaned_data['login']));
		}
		return $this->cleaned_data['login'];
	}

	/**
	 * Check the terms.
	 */
	public function clean_terms() {
		if (!$this->cleaned_data['terms']) {
			throw new Gatuf_Form_Invalid('Sabemos que es aburrido, pero tienes que aceptar los términos y condiciones');
		}
		return $this->cleaned_data['terms'];
	}

	function clean_email() {
		$this->cleaned_data['email'] = mb_strtolower(trim($this->cleaned_data['email']));
		$guser = new Registro_User();
		$sql = new Gatuf_SQL('email=%s', $this->cleaned_data['email']);
		if ($guser->getCount(array('filter' => $sql->gen())) > 0) {
			throw new Gatuf_Form_Invalid(sprintf('El correo "%s" ya está en uso. FIXME: Puedes probar a recuperar tu contraseña', $this->cleaned_data['email']));
		}
		return $this->cleaned_data['email'];
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
			throw new Exception('Cannot save the model from an invalid form.');
		}
		$user = new Registro_User();
		$user->first_name = '---'; // with both this set and
								   // active==false we can find later
								   // on, all the unconfirmed accounts
								   // that could be purged.
		$user->last_name = $this->cleaned_data['login'];
		$user->login = $this->cleaned_data['login'];
		$user->email = $this->cleaned_data['email'];
		$user->active = false;
		$user->create();
		self::sendVerificationEmail($user);
		return $user;
	}

	public static function sendVerificationEmail($user) {
		Gatuf::loadFunction('Gatuf_HTTP_URL_urlForView');
		$from_email = Gatuf::config('from_email');
		$cr = new Gatuf_Crypt(md5(Gatuf::config('secret_key')));
		$encrypted = trim($cr->encrypt($user->email.':'.$user->id), '~');
		$key = substr(md5(Gatuf::config('secret_key').$encrypted), 0, 2).$encrypted;
		$url = Gatuf::config('url_base').Gatuf_HTTP_URL_urlForView('Registro_Views::registerConfirmation', array($key), array(), false);
		$urlik = Gatuf::config('url_base').Gatuf_HTTP_URL_urlForView('Registro_Views::registerInputKey', array(), array(), false);
		$context = new Gatuf_Template_Context(
			 array('key' => $key,
				   'url' => $url,
				   'urlik' => $urlik,
				   'user'=> $user,
				   )
											 );
		$tmpl = new Gatuf_Template('registro/register/confirmation-email.txt');
		$text_email = $tmpl->render($context);
		$email = new Gatuf_Mail($from_email, $user->email,
							   'Cursos de Invierno - Confirma tu cuenta');
		$email->addTextMessage($text_email);
		$email->sendMail();
	}
}
