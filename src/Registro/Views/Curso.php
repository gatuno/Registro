<?php

Gatuf::loadFunction('Gatuf_HTTP_URL_urlForView');
Gatuf::loadFunction('Gatuf_Shortcuts_RenderToResponse');

class Registro_Views_Curso {
	public function index ($request, $match) {
		$curso = new Registro_Curso ();
		
		$pag = new Gatuf_Paginator ($curso);
		$pag->action = array ('Registro_Views_Curso::index');
		$pag->summary = 'Lista de cursos';
		
		$list_display = array (
			array ('titulo', 'Gatuf_Paginator_FKLink', 'Titulo'),
			array ('descripcion', 'Gatuf_Paginator_FKExtra', 'Descripción'),
			array ('ponente', 'Gatuf_Paginator_FKExtra', 'Ponente'),
		);
		
		$pag->items_per_page = 20;
		$pag->no_results_text = 'No hay cursos registrados';
		$pag->max_number_pages = 7;
		
		$pag->configure ($list_display,
			array ('descripcion', 'titulo'),
			array ('titulo')
		);
		
		$pag->setFromRequest ($request);
		
		return Gatuf_Shortcuts_RenderToResponse ('registro/curso/index.html',
		                                         array('page_title' => 'Cursos',
                                                       'paginador' => $pag),
                                                 $request);
	}
	
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
	
	public function verCurso ($request, $match) {
		$curso = new Registro_Curso ();
		
		if ($curso->get ($match[1]) === false) {
			return new Gatuf_HTTP_Error404();
		}
		
		return Gatuf_Shortcuts_RenderToResponse('registro/curso/ver.html', 
		                                         array('page_title' => 'Curso: '.$curso->titulo,
		                                         'curso' => $curso),
		                                         $request);
	}
}
