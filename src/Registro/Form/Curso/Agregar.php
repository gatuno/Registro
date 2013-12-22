<?php

class Registro_Form_Curso_Agregar extends Gatuf_Form {
	private $user;
	public function initFields($extra=array()) {
		$this->user = $extra['user'];
		
		$this->fields['titulo'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Titulo',
				'max_length' => 199,
				'min_length' => 3,
				'help_text' => 'El titulo del curso',
				'widget_attrs' => array(
					'maxlength' => 199,
					'size' => 50,
				),
		));
		
		$this->fields['descripcion'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Descripción',
				'help_text' => 'Una descripción del curso',
				'widget' => 'Gatuf_Form_Widget_HtmlareaInput',
		));
		
		$this->fields['requisitos'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Requisitos',
				'help_text' => 'Posibles requisitos del curso',
				'widget' => 'Gatuf_Form_Widget_HtmlareaInput',
		));
		
		if ($this->user->administrator) {
			$choices = array ();
			
			foreach (Gatuf::factory ('Registro_User')->getList (array ('filter' => 'staff=1 OR administrator=1')) as $user) {
				$choices[(string)$user] = $user->id;
			}
			
			$this->fields['ponente'] = new Gatuf_Form_Field_Integer (
				array (
					'required' => true,
					'label' => 'Ponente',
					'help_text' => 'El ponente del curso',
					'widget' => 'Gatuf_Form_Widget_SelectInput',
					'widget_attrs' => array (
						'choices' => $choices,
					),
			));
		}
	}
	
	public function save ($commit = true) {
		if (!$this->isValid()) {
			throw new Exception('Cannot save the model from an invalid form.');
		}
		
		$curso = new Registro_Curso ();
		
		$curso->setFromFormData ($this->cleaned_data);
		
		if (!$this->user->administrator) $curso->ponente = $this->user;
		
		if ($commit) $curso->create ();
		
		return $curso;
	}
}
