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
 * Check the validity of a confirmation key.
 *
 */
class Registro_Form_RegisterInputKey extends Gatuf_Form {
	public function initFields($extra=array()) {
		$this->fields['key'] = new Gatuf_Form_Field_Varchar(
			array(
				'required' => true,
				'label' => 'Tu clave de confirmación',
				'initial' => '',
				'widget_attrs' => array(
					'size' => 50,
				),
		));
	}

	/**
	 * Validate the key.
	 */
	public function clean_key() {
		$this->cleaned_data['key'] = trim($this->cleaned_data['key']);
		$error = 'La clave de confirmación no es válida. Deberías copiarla y pegarla directamente desde tu correo.';
		if (false === ($email_id=self::checkKeyHash($this->cleaned_data['key']))) {
			throw new Gatuf_Form_Invalid($error);
		}
		$guser = new Registro_User();
		$sql = new Gatuf_SQL('email=%s AND id=%s', $email_id);
		if ($guser->getCount(array('filter' => $sql->gen())) != 1) {
			throw new Gatuf_Form_Invalid($error);
		}
		return $this->cleaned_data['key'];
	}

	/**
	 * Save the model in the database.
	 *
	 * @param bool Commit in the database or not. If not, the object
	 *			 is returned but not saved in the database.
	 * @return string Url to redirect to the form.
	 */
	function save($commit=true) {
		if (!$this->isValid()) {
			throw new Exception('Cannot save an invalid form.');
		}
		return Gatuf_HTTP_URL_urlForView('Gatuf_Views::registerConfirmation',
										array($this->cleaned_data['key']));
	}

	/**
	 * Return false or an array with the email and id.
	 *
	 * This is a static function to be reused by other forms.
	 *
	 * @param string Confirmation key
	 * @return mixed Either false or array(email, id)
	 */
	public static function checkKeyHash($key) {
		$hash = substr($key, 0, 2);
		$encrypted = substr($key, 2);
		if ($hash != substr(md5(Gatuf::f('secret_key').$encrypted), 0, 2)) {
			return false;
		}
		$cr = new Gatuf_Crypt(md5(Gatuf::f('secret_key')));
		return explode(':', $cr->decrypt($encrypted), 2);
	}
}
