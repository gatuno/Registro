<?php

class Registro_User extends Gatuf_User {
	function extended_init () {
		$this->_a['cols']['codigo'] = array (
			'type' => 'Gatuf_DB_Field_Char',
			'blank' => false,
			'size' => 9,
		);
		
		$this->_a['cols']['carrera'] = array (
			'type' => 'Gatuf_DB_Field_Foreignkey',
			'model' => 'Calif_Carrera',
			'blank' => false,
		);
		
		/* Poner demás cosas */
	}
}
