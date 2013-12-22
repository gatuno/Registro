<?php

Gatuf::loadFunction('Gatuf_HTTP_URL_urlForView');
Gatuf::loadFunction('Gatuf_Shortcuts_RenderToResponse');

class Registro_Views_Curso {
	public $agregar_precond = array ('Gatuf_Precondition::staffRequired');
	public function agregar ($request, $match) {
		$extra = array ('user' => $request->user);
		
		if ($request->method == 'POST') {
			$form = new Registro_Form_Curso_Agregar ($request->POST, $extra);
			
			if ($form->isValid ()) {
				$curso = $form->save ();
				
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views::index');
				
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		} else {
			$form = new Registro_Form_Curso_Agregar (null, $extra);
		}
		
		return Gatuf_Shortcuts_RenderToResponse('registro/curso/agregar.html', 
		                                         array('page_title' => 'Nuevo curso',
		                                         'form' => $form),
		                                         $request);
	}
}
