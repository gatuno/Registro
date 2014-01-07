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
			'conocimiento' =>
			array (
			       'type' => 'Gatuf_DB_Field_Text',
			       'blank' => false,
			),
			'horario' =>
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
			'contacto' =>
			array (
			       'type' => 'Gatuf_DB_Field_Email',
			       'blank' => false,
			),
			'alumnos' =>
			array (
			       'type' => 'Gatuf_DB_Field_Manytomany',
			       'model' => 'Registro_User',
			       'relate_name' => 'cursos',
			),
			'cupo' =>
			array (
			       'type' => 'Gatuf_DB_Field_Integer',
			       'blank' => false,
			       'default' => 0,
			),
		);
	}
	
	function displaylinkedtitulo ($extra = null) {
		return '<a href="'.Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $this->id).'">'.$this->titulo.'</a>';
	}
	
	function displaydescripcion ($extra = null) {
		$text = strip_tags ($this->descripcion);
		
		if (strlen ($text) > 200) {
			return substr ($text, 0, 200).'... '.'<a href="'.Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $this->id).'">Ver más'.'</a>';
		}
		return $text;
	}
	
	function displayponente ($extra = null) {
		return (string) $this->get_ponente ();
	}
}
