<?php

class Registro_Curso extends Gatuf_Model {
	/* Manejador de la tabla de cursos */
	public $_model = __CLASS__;
	
	function init () {
		$this->_a['table'] = 'cursos';
		$this->_a['model'] = __CLASS__;
		$this->primary_key = 'id';
		
		$this->_a['cols'] = array (
			'id' =>
			array (
			       'type' => 'Gatuf_DB_Field_Sequence',
			       'blank' => false,
			),
			'titulo' =>
			array (
			       'type' => 'Gatuf_DB_Field_Varchar',
			       'size' => 200,
			       'blank' => false,
			),
			'descripcion' =>
			array (
			       'type' => 'Gatuf_DB_Field_Text',
			       'blank' => false,
			),
			'requisitos' =>
			array (
			       'type' => 'Gatuf_DB_Field_Text',
			       'blank' => false,
			),
			'ponente' =>
			array (
			       'type' => 'Gatuf_DB_Field_Foreignkey',
			       'model' => 'Registro_User',
			       'relate_name' => 'ponente',
			),
			'alumnos' =>
			array (
			       'type' => 'Gatuf_DB_Field_Manytomany',
			       'model' => 'Registro_User',
			       'relate_name' => 'alumnos',
			),
		);
	}
	
	function displaylinkedtitulo ($extra = null) {
		return $this->titulo;
	}
	
	function displaydescripcion ($extra = null) {
		return strip_tags ($this->descripcion);
	}
	
	function displayponente ($extra = null) {
		$user = new Registro_User ($this->ponente);
		
		return (string) $user;
	}
}
