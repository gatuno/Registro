<?php

class Registro_Form_SiteAdmin extends Gatuf_Form {
	public function initfields ($extra = array ()) {
		$gconf = new Gatuf_GSetting ();
		$gconf->setApp ('Registro');
		
		$matricular = $gconf->getVal ('matriculaciones_activas', 'F');
		$this->fields['matriculaciones'] = new Gatuf_Form_Field_Boolean (
			array (
				'required' => true,
				'label' => 'Matriculaciones activas',
				'initial' => ($matricular == 'F' ? false : true),
				'help_text' => 'Si se permiten matriculaciones sobre los cursos'
		));
	}
	
	public function save ($commit = true) {
		if (!$this->isValid ()) {
			throw new Exception ('Cannot save an invalid form');
		}
		
		$matricular = $this->cleaned_data ['matriculaciones'];
		
		$gconf = new Gatuf_GSetting ();
		$gconf->setApp ('Registro');
		
		if ($commit) {
			$m = ($matricular ? 'T' : 'F');
			
			$gconf->setVal ('matriculaciones_activas', $m);
		}
	}
}
