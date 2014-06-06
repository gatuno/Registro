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
		
		$this->fields['conocimiento'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Conocimiento previos',
				'help_text' => 'Lista de conocimientos previos deseables en el curso',
				'widget' => 'Gatuf_Form_Widget_HtmlareaInput',
		));
		
		$this->fields['horario'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Horario',
				'help_text' => 'Muy importante especificar el horario. En caso de no contar con un aula, escribir aula pendiente.',
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
			
			$this->fields['requiere_ife'] = new Gatuf_Form_Field_Boolean (
				array (
					'required' => true,
					'initial' => false,
					'label' => 'Requiere IFE',
					'help_text' => 'Si este curso requiere que el alumno tenga su IFE subida antes de matricularse',
					'widget' => 'Gatuf_Form_Widget_CheckboxInput',
			));
			
			$this->fields['requiere_curp'] = new Gatuf_Form_Field_Boolean (
				array (
					'required' => true,
					'initial' => false,
					'label' => 'Requiere Curp',
					'help_text' => 'Si este curso requiere que el alumno tenga su Curp subida antes de matricularse',
					'widget' => 'Gatuf_Form_Widget_CheckboxInput',
			));
		}
		
		$this->fields['contacto'] = new Gatuf_Form_Field_Email (
			array (
				'required' => true,
				'label' => 'Correo de contacto',
				'help_text' => 'Este correo estará disponible publicamente',
		));
		
		$this->fields['cupo'] = new Gatuf_Form_Field_Integer (
			array (
				'required' => true,
				'label' => 'Cupo',
				'initial' => 24,
				'help_text' => 'La capacidad máxima del curso. Mínimo 1, máximo 30',
				'min' => 1,
				'max' => 60,
		));
	}
	
	function clean_descripcion () {
		$data = $this->cleaned_data['descripcion'];
		
		if (substr ($data, -4) == '<br>') {
			$data = substr ($data, 0, -4);
		}
		
		if ($data === '') {
			throw new Gatuf_Form_Invalid ('Este campo es obligatorio');
		}
		
		return $data;
	}
	
	function clean_requisitos () {
		$data = $this->cleaned_data['requisitos'];
		
		if (substr ($data, -4) == '<br>') {
			$data = substr ($data, 0, -4);
		}
		
		if ($data === '') {
			throw new Gatuf_Form_Invalid ('Este campo es obligatorio');
		}
		
		return $data;
	}
	
	function clean_conocimiento () {
		$data = $this->cleaned_data['conocimiento'];
		
		if (substr ($data, -4) == '<br>') {
			$data = substr ($data, 0, -4);
		}
		
		if ($data === '') {
			throw new Gatuf_Form_Invalid ('Este campo es obligatorio');
		}
		
		return $data;
	}
	
	function clean_horario () {
		$data = $this->cleaned_data['horario'];
		
		if (substr ($data, -4) == '<br>') {
			$data = substr ($data, 0, -4);
		}
		
		if ($data === '') {
			throw new Gatuf_Form_Invalid ('Este campo es obligatorio');
		}
		
		return $data;
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
