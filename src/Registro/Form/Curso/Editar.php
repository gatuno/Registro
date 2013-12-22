<?php

class Registro_Form_Curso_Editar extends Gatuf_Form {
	private $user;
	private $curso;
	
	public function initFields($extra=array()) {
		$this->user = $extra['user'];
		$this->curso = $extra['curso'];
		
		$this->fields['descripcion'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Descripción',
				'initial' => $this->curso->descripcion,
				'help_text' => 'Una descripción del curso',
				'widget' => 'Gatuf_Form_Widget_HtmlareaInput',
		));
		
		$this->fields['requisitos'] = new Gatuf_Form_Field_Varchar (
			array (
				'required' => true,
				'label' => 'Requisitos',
				'initial' => $this->curso->requisitos,
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
					'initial' => $this->curso->ponente,
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
		
		$this->curso->setFromFormData ($this->cleaned_data);
		
		if (!$this->user->administrator) $this->curso->ponente = $this->user;
		
		if ($commit) $this->curso->update ();
		
		return $this->curso;
	}
}
