<?php

class Registro_User extends Gatuf_User {
	function extended_init () {
		$this->_a['model'] = 'Registro_User';
		$this->_model = 'Registro_User';
		
		$this->_a['cols']['codigo'] = array (
			'type' => 'Gatuf_DB_Field_Char',
			'blank' => false,
			'size' => 9,
			'is_null' => true,
			'default' => null,
		);
		
		$this->_a['cols']['carrera'] = array (
			'type' => 'Gatuf_DB_Field_Foreignkey',
			'model' => 'Calif_Carrera',
			'blank' => false,
			'is_null' => true,
			'default' => null,
		);
		
		$this->_a['cols']['escuela'] = array (
			'type' => 'Gatuf_DB_Field_Varchar',
			'blank' => false,
			'is_null' => true,
			'default' => null,
		);
		
		/* Poner demás cosas */
	}
}
