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
				
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $curso->id);
				
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
	
	public $editar_precond = array ('Gatuf_Precondition::staffRequired');
	public function editar ($request, $match) {
		$curso = new Registro_Curso ();
		
		if ($curso->get ($match[1]) === false) {
			return new Gatuf_HTTP_Error404();
		}
		
		if (!$request->user->administrator) {
			if ($request->user->id != $curso->ponente) {
				$request->user->setMessage (3, 'No puedes modificar cursos que no son tuyos');
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $curso->id);
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		}
		
		$extra = array ('user' => $request->user, 'curso' => $curso);
		if ($request->method == 'POST') {
			$form = new Registro_Form_Curso_Editar ($request->POST, $extra);
			
			if ($form->isValid ()) {
				$curso = $form->save ();
				
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $curso->id);
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		} else {
			$form = new Registro_Form_Curso_Editar (null, $extra);
		}
		
		return Gatuf_Shortcuts_RenderToResponse('registro/curso/editar.html', 
		                                         array('page_title' => 'Actualizar curso',
		                                         'form' => $form,
		                                         'curso' => $curso),
		                                         $request);
	}
	
	public function verCurso ($request, $match) {
		$curso = new Registro_Curso ();
		
		if ($curso->get ($match[1]) === false) {
			return new Gatuf_HTTP_Error404();
		}
		$registrados = $request->user->get_cursos_list ();
		
		$matriculado = false;
		foreach ($registrados as $un_curso) {
			if ($curso->id == $un_curso->id) $matriculado = true;
		}
		
		return Gatuf_Shortcuts_RenderToResponse('registro/curso/ver.html', 
		                                         array('page_title' => $curso->titulo,
		                                         'curso' => $curso,
		                                         'matriculado' => $matriculado),
		                                         $request);
	}
	
	public $matricular_precond = array ('Gatuf_Precondition::loginRequired');
	public function matricular ($request, $match) {
		$curso = new Registro_Curso ();
		
		if ($curso->get ($match[1]) === false) {
			return new Gatuf_HTTP_Error404();
		}
		$url = Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $curso->id);
		
		$registrados = $request->user->get_cursos_list ();
		
		foreach ($registrados as $registrado) {
			if ($registrado->id == $curso->id) {
				$request->user->setMessage (1, 'Ya estás registrado a este curso');
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		}
		
		$gconf = new Gatuf_GSetting ();
		$gconf->setApp ('Registro');
		
		if ($gconf->getVal ('matriculaciones_activas', 'F') == 'F') {
			/* Las matriculaciones no están activas */
			$request->user->setMessage (3, 'El periodo de matriculaciones a los cursos está cerrado');
			return new Gatuf_HTTP_Response_Redirect ($url);
		}
		
		if (count ($registrados) >= 2) {
			$request->user->setMessage (2, 'El máximo permitido de cursos simultaneos es de 2. Elimina algun curso para matricularte a este curso.');
			return new Gatuf_HTTP_Response_Redirect ($url);
		}
		
		$matriculados = $curso->get_alumnos_list ();
		
		if (count ($matriculados) >= $curso->cupo) {
			$request->user->setMessage (2, 'Lo sentimos, este curso está lleno.');
			return new Gatuf_HTTP_Response_Redirect ($url);
		}
		
		if ($curso->requiere_ife && $request->user->ife == '') {
			$request->user->setMessage (2, 'El curso requiere que subas tu credencial IFE antes de matricularte. Puedes subir esta información desde tu Perfil');
			return new Gatuf_HTTP_Response_Redirect ($url);
		}
		
		if ($curso->requiere_curp && $request->user->curp == '') {
			$request->user->setMessage (2, 'El curso requiere que subas tu Curp antes de matricularte. Puedes subir esta información desde tu Perfil');
			return new Gatuf_HTTP_Response_Redirect ($url);
		}
		
		$curso->setAssoc ($request->user);
		
		$request->user->setMessage (1, 'Bienvenido al curso "'.$curso->titulo.'"');
		return new Gatuf_HTTP_Response_Redirect ($url);
	}
	
	public function desmatricular ($request, $match) {
		$curso = new Registro_Curso ();
		
		if ($curso->get ($match[1]) === false) {
			return new Gatuf_HTTP_Error404();
		}
		
		$registrados = $request->user->get_cursos_list ();
		$dentro = false;
		foreach ($registrados as $registrado) {
			if ($registrado->id == $curso->id) $dentro = true;
		}
		
		if (!$dentro) {
			$request->user->setMessage (3, 'No te encuentras matriculado a este curso');
		}
		
		if ($request->method == 'POST') {
			$curso->delAssoc ($request->user);
			$request->user->setMessage (1, 'Te has desmatriculado del curso "'.$curso->titulo.'"');
			
			$gconf = new Gatuf_GSetting ();
			$gconf->setApp ('Registro');
		
			if ($gconf->getVal ('matriculaciones_activas', 'F') == 'F') {
				/* Las matriculaciones no están activas */
				$request->user->setMessage (2, 'El periodo de matriculaciones a los cursos está cerrado. No podrás volver entrar a este curso.');
			}
			$url = Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $curso->id);
			return new Gatuf_HTTP_Response_Redirect ($url);
		}
		
		return Gatuf_Shortcuts_RenderToResponse('registro/curso/desmatricular.html', 
		                                         array('page_title' => 'Desmatricular del curso',
		                                         'curso' => $curso),
		                                         $request);
	}
	
	public $verLista_precond = array ('Gatuf_Precondition::staffRequired');
	public function verLista ($request, $match) {
		$curso = new Registro_Curso ();
		
		if ($curso->get ($match[1]) === false) {
			return new Gatuf_HTTP_Error404();
		}
		
		if (!$request->user->administrator) {
			if ($request->user->id != $curso->ponente) {
				$request->user->setMessage (3, 'No puedes visualizar listas de cursos que no son tuyos');
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $curso->id);
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		}
		$alumnos = $curso->get_alumnos_list (array ('order' => 'last_name ASC, first_name ASC'));
		
		return Gatuf_Shortcuts_RenderToResponse('registro/curso/lista.html', 
		                                         array('page_title' => 'Lista de alumnos',
		                                         'curso' => $curso,
		                                         'alumnos' => $alumnos),
		                                         $request);
	}
	
	public $descargarListaODS_precond = array ('Gatuf_Precondition::staffRequired');
	public function descargarListaODS ($request, $match) {
		$curso = new Registro_Curso ();
		
		if ($curso->get ($match[1]) === false) {
			return new Gatuf_HTTP_Error404();
		}
		
		if (!$request->user->administrator) {
			if ($request->user->id != $curso->ponente) {
				$request->user->setMessage (3, 'No puedes visualizar listas de cursos que no son tuyos');
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views_Curso::verCurso', $curso->id);
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		}
		$alumnos = $curso->get_alumnos_list (array ('order' => 'last_name ASC, first_name ASC'));
		
		$libro_ods = new Gatuf_ODS ();
		
		$libro_ods->addNewSheet ('Principal');
		$libro_ods->addStringCell ('Principal', 1, 1, 'Nick');
		$libro_ods->addStringCell ('Principal', 1, 2, 'Apellido');
		$libro_ods->addStringCell ('Principal', 1, 3, 'Nombre');
		$libro_ods->addStringCell ('Principal', 1, 4, 'Correo');
		$libro_ods->addStringCell ('Principal', 1, 5, 'Escuela');
		$libro_ods->addStringCell ('Principal', 1, 6, 'Carrera');
		
		$g = 2;
		foreach ($alumnos as $alumno) {
			$libro_ods->addStringCell ('Principal', $g, 1, $alumno->login);
			$libro_ods->addStringCell ('Principal', $g, 2, $alumno->last_name);
			$libro_ods->addStringCell ('Principal', $g, 3, $alumno->first_name);
			$libro_ods->addStringCell ('Principal', $g, 4, $alumno->email);
			$libro_ods->addStringCell ('Principal', $g, 5, $alumno->escuela);
			$libro_ods->addStringCell ('Principal', $g, 6, (string) $alumno->get_carrera ());
			
			$g++;
		}
		
		$libro_ods->construir_paquete ();
		
		return new Gatuf_HTTP_Response_File ($libro_ods->nombre, 'Lista_curso_'.$curso->id.'.ods', 'application/vnd.oasis.opendocument.spreadsheet', true);
	}
}
