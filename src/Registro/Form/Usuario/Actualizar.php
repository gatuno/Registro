<?php

class Registro_Form_Usuario_Actualizar extends Gatuf_Form {
	private $user;
	
	public function initFields($extra=array()) {
		$this->user = $extra['user'];
		
		$upload_path = Gatuf::config ('user_data_upload');
		$curp_filename = sprintf ('%s/curp_%%s', $this->user->login);
		$this->fields['attachment_curp'] = new Gatuf_Form_Field_File (
			array (
				'required' => false,
				'label' => 'Curp',
				'help_text' => 'Su imagen o archivo pdf',
				'move_function_params' => array (
					'upload_path' => $upload_path,
					'upload_path_create' => true,
					'file_name' => $curp_filename,
					'upload_overwrite' => true,
				),
		));
		
		$this->fields['borrar_curp'] = new Gatuf_Form_Field_Boolean (
			array (
				'required' => false,
				'label' => 'Borrar curp',
				'initial' => false,
				'widget' => 'Gatuf_Form_Widget_CheckboxInput',
		));
		
		$ife_filename = sprintf ('%s/ife_%%s', $this->user->login);
		$this->fields['attachment_ife'] = new Gatuf_Form_Field_File (
			array (
				'required' => false,
				'label' => 'IFE',
				'help_text' => 'Una imagen clara. Evita subir fotografías borrosas',
				'move_function_params' => array (
					'upload_path' => $upload_path,
					'upload_path_create' => true,
					'file_name' => $ife_filename,
					'upload_overwrite' => true,
				),
		));
		
		$this->fields['borrar_ife'] = new Gatuf_Form_Field_Boolean (
			array (
				'required' => false,
				'label' => 'Borrar IFE',
				'initial' => false,
				'widget' => 'Gatuf_Form_Widget_CheckboxInput',
		));
	}
	
	function clean_attachment_curp () {
		// Just png, jpeg/jpg, gif or pdf
		if (!preg_match('/\.(png|jpg|jpeg|gif|pdf)$/i', $this->cleaned_data['attachment_curp']) && $this->cleaned_data['attachment_curp'] != '') {
			@unlink(Gatuf::config('user_data_upload').'/'.$this->cleaned_data['attachment_curp']);
			throw new Gatuf_Form_Invalid('Por razones de seguridad, no puedes subir un archivo con esta extensión');
		}
		return $this->cleaned_data['attachment_curp'];
	}
	
	function clean_attachment_ife () {
		// Just png, jpeg/jpg, gif or pdf
		if (!preg_match('/\.(png|jpg|jpeg|gif|pdf)$/i', $this->cleaned_data['attachment_ife']) && $this->cleaned_data['attachment_ife'] != '') {
			@unlink(Gatuf::config('user_data_upload').'/'.$this->cleaned_data['attachment_ife']);
			throw new Gatuf_Form_Invalid('Por razones de seguridad, no puedes subir un archivo con esta extensión');
		}
		return $this->cleaned_data['attachment_ife'];
	}
	
	function save ($commit = true) {
		if (!$this->isValid ()) {
			throw new Exception ('Cannot save a invalid form');
		}
		
		if ($commit) {
			if ($this->user->curp != '' && ($this->cleaned_data['borrar_curp'] == 1 || $this->cleaned_data['attachment_curp'] != '')) {
				$curp_path = Gatuf::config ('user_data_upload').'/'.$this->user->curp;
				if (basename ($curp_path) != '' && is_file ($curp_path)) {
					unlink ($curp_path);
				}
				$this->user->curp = '';
			}
			
			if ($this->cleaned_data['attachment_curp'] != '') {
				$this->user->curp = $this->cleaned_data['attachment_curp'];
			}
			
			if ($this->user->ife != '' && ($this->cleaned_data['borrar_ife'] == 1 || $this->cleaned_data['attachment_ife'] != '')) {
				$ife_path = Gatuf::config ('user_data_upload').'/'.$this->user->ife;
				if (basename ($ife_path) != '' && is_file ($ife_path)) {
					unlink ($ife_path);
				}
				$this->user->ife = '';
			}
			
			if ($this->cleaned_data['attachment_ife'] != '') {
				$this->user->ife = $this->cleaned_data['attachment_ife'];
			}
			
			$this->user->update ();
		}
	}
}
