<?php

Gatuf::loadFunction('Gatuf_HTTP_URL_urlForView');
Gatuf::loadFunction('Gatuf_Shortcuts_RenderToResponse');

class Registro_Views {
	function index ($request, $match) {
		return Gatuf_Shortcuts_RenderToResponse ('registro/index.html', array (), $request);
	}
	
	function login ($request, $match, $success_url = '', $extra_context=array()) {
		if (!empty($request->REQUEST['_redirect_after'])) {
			$success_url = $request->REQUEST['_redirect_after'];
		} else {
			$success_url = Gatuf::config('registro_base').Gatuf::config ('login_success_url', '/');
		}
		
		$error = '';
		if ($request->method == 'POST') {
			foreach (Gatuf::config ('auth_backends', array ('Gatuf_Auth_ModelBackend')) as $backend) {
				$user = call_user_func (array ($backend, 'authenticate'), $request->POST);
				if ($user !== false) {
					break;
				}
			}
			
			if (false === $user) {
				$error = 'El usuario o la contraseña no son válidos. El usuario y la contraseña son sensibles a las mayúsculas';
			} else {
				if (!$request->session->getTestCookie ()) {
					$error = 'Necesitas habilitar las cookies para acceder a este sitio';
				} else {
					$request->user = $user;
					$request->session->clear ();
					$request->session->setData('login_time', gmdate('Y-m-d H:i:s'));
					$user->last_login = gmdate('Y-m-d H:i:s');
					$user->update ();
					$request->session->deleteTestCookie ();
					return new Gatuf_HTTP_Response_Redirect ($success_url);
				}
				
			}
		}
		/* Mostrar el formulario de login */
		$request->session->createTestCookie ();
		$context = new Gatuf_Template_Context_Request ($request, array ('page_title' => 'Ingresar',
		'_redirect_after' => $success_url,
		'error' => $error));
		$tmpl = new Gatuf_Template ('registro/login_form.html');
		return new Gatuf_HTTP_Response ($tmpl->render ($context));
	}
	
	function logout ($request, $match) {
		$success_url = Gatuf::config ('after_logout_page', '/');
		$user_model = Gatuf::config('gatuf_custom_user','Gatuf_User');
		
		$request->user = new $user_model ();
		$request->session->clear ();
		$request->session->setData ('logout_time', gmdate('Y-m-d H:i:s'));
		if (0 !== strpos ($success_url, 'http')) {
			$murl = new Gatuf_HTTP_URL ();
			$success_url = Gatuf::config('registro_base').$murl->generate($success_url);
		}
		
		return new Gatuf_HTTP_Response_Redirect ($success_url);
	}
	
	function register($request, $match) {
		$title = 'Crear tu cuenta';
		$params = array('request'=>$request);
		if ($request->method == 'POST') {
			$form = new Registro_Form_Register (array_merge((array)$request->POST, (array)$request->FILES), $params);
			if ($form->isValid()) {
				$user = $form->save(); // It is sending the confirmation email
				$url = Gatuf_HTTP_URL_urlForView('Registro_Views::registerInputKey');
				return new Gatuf_HTTP_Response_Redirect($url);
			}
		} else {
			if (isset($request->GET['login'])) {
				$params['initial'] = array('login' => $request->GET['login']);
			}
			$form = new Registro_Form_Register(null, $params);
		}
		$context = new Gatuf_Template_Context(array());
		$tmpl = new Gatuf_Template('registro/terms.html');
		$terms = Gatuf_Template::markSafe($tmpl->render($context));
		return Gatuf_Shortcuts_RenderToResponse('registro/register/index.html', 
		                                         array('page_title' => $title,
		                                         'form' => $form,
		                                         'terms' => $terms),
		                                         $request);
	}
    
	function registerInputKey($request, $match) {
		$title = 'Confirma la creación de tu cuenta';
		if ($request->method == 'POST') {
			$form = new Registro_Form_RegisterInputKey($request->POST);
			if ($form->isValid()) {
			$url = $form->save();
				return new Gatuf_HTTP_Response_Redirect($url);
			}
		} else {
			$form = new Registro_Form_RegisterInputKey();
		}
		return Gatuf_Shortcuts_RenderToResponse('registro/register/inputkey.html', 
		                                         array('page_title' => $title,
		                                         'form' => $form),
		                                         $request);
	}
	
	function registerConfirmation($request, $match) {
		$title = 'Confirma la creación de tu cuenta';
		$key = $match[1];
		// first "check", full check is done in the form.
		$email_id = Registro_Form_RegisterInputKey::checkKeyHash($key);
		if (false == $email_id) {
			$url = Gatuf_HTTP_URL_urlForView('Registro_Views::registerInputKey');
			return new Gatuf_HTTP_Response_Redirect($url);
		}
		$user = new Registro_User($email_id[1]);
		$extra = array('key' => $key,
		'user' => $user);
		if ($request->method == 'POST') {
			$form = new Registro_Form_RegisterConfirmation($request->POST, $extra);
			if ($form->isValid()) {
				$user = $form->save();
				$request->user = $user;
				$request->session->clear();
				$request->session->setData('login_time', gmdate('Y-m-d H:i:s'));
				$user->last_login = gmdate('Y-m-d H:i:s');
				$user->update();
				$request->user->setMessage(1, '¡Bienvenido! Ahora puedes participar en el curso de invierno de tu elección');
				$url = Gatuf_HTTP_URL_urlForView('Registro_Views::index');
				return new Gatuf_HTTP_Response_Redirect($url);
			}
		} else {
			$form = new Registro_Form_RegisterConfirmation(null, $extra);
		}
		return Gatuf_Shortcuts_RenderToResponse('registro/register/confirmation.html', 
		                                         array('page_title' => $title,
		                                         'new_user' => $user,
		                                         'form' => $form),
		                                         $request);
	}
	
	function passwordRecoveryAsk ($request, $match) {
		$title = 'Recuperar contraseña';
		
		if ($request->method == 'POST') {
			$form = new Registro_Form_Password ($request->POST);
			if ($form->isValid ()) {
				$url = $form->save ();
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		} else {
			$form = new Registro_Form_Password ();
		}
		
		return Gatuf_Shortcuts_RenderToResponse ('registro/user/recuperarcontra-ask.html',
		                                         array ('page_title' => $title,
		                                         'form' => $form),
		                                         $request);
	}
	
	function passwordRecoveryInputCode ($request, $match) {
		$title = 'Recuperar contraseña';
		if ($request->method == 'POST') {
			$form = new Registro_Form_PasswordInputKey($request->POST);
			if ($form->isValid ()) {
				$url = $form->save ();
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		} else {
		 	$form = new Registro_Form_PasswordInputKey ();
		}
		
		return Gatuf_Shortcuts_RenderToResponse ('registro/user/recuperarcontra-codigo.html',
		                                         array ('page_title' => $title,
		                                         'form' => $form),
		                                         $request);
	}
	
	function passwordRecovery ($request, $match) {
		$title = 'Recuperar contraseña';
		$key = $match[1];
		
		$email_id = Registro_Form_PasswordInputKey::checkKeyHash($key);
		if (false == $email_id) {
			$url = Gatuf_HTTP_URL_urlForView ('Registro_Views::passwordRecoveryInputCode');
			return new Gatuf_HTTP_Response_Redirect ($url);
		}
		$user = new Gatuf_User ($email_id[1]);
		$extra = array ('key' => $key,
		                'user' => $user);
		if ($request->method == 'POST') {
			$form = new Registro_Form_PasswordReset($request->POST, $extra);
			if ($form->isValid()) {
				$user = $form->save();
				$request->user = $user;
				$request->session->clear();
				$request->session->setData('login_time', gmdate('Y-m-d H:i:s'));
				$user->last_login = gmdate('Y-m-d H:i:s');
				$user->update ();
				/* Establecer un mensaje */
				$request->user->setMessage(1, 'Bienvenido de nuevo');
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views::index');
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		} else {
			$form = new Registro_Form_PasswordReset (null, $extra);
		}
		return Gatuf_Shortcuts_RenderToResponse ('registro/user/recuperarcontra.html',
		                                         array ('page_title' => $title,
		                                         'new_user' => $user,
		                                         'form' => $form),
		                                         $request);
	}
	
	public $dashboard_precond = array ('Gatuf_Precondition::loginRequired');
	function dashboard ($request, $match) {
		return Gatuf_Shortcuts_RenderToResponse ('registro/user/dashboard.html',
		                                         array ('page_title' => 'Perfil público'),
		                                         $request);
	}
	
	public $siteAdmin_precond = array ('Gatuf_Precondition::adminRequired');
	function siteAdmin ($request, $match) {
		if ($request->method == 'POST') {
			$form = new Registro_Form_SiteAdmin ($request->POST);
			
			if ($form->isValid ()) {
				$form->save ();
				
				$request->user->setMessage (1, 'Preferencias del sitio guardadas');
				
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views::index');
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		} else {
			$form = new Registro_Form_SiteAdmin (null);
		}
		
		return Gatuf_Shortcuts_RenderToResponse ('registro/site-admin.html',
		                                         array ('page_title' => 'Administración del sitio',
		                                         'form' => $form),
		                                         $request);
	}
	
	public $actualizar_precond = array ('Gatuf_Precondition::loginRequired');
	public function actualizar ($request, $match) {
		$extra = array ('user' => $request->user);
		
		if ($request->method == 'POST') {
			$form = new Registro_Form_Usuario_Actualizar (array_merge ($request->POST, $request->FILES), $extra);
			
			if ($form->isValid ()) {
				$form->save ();
				
				$url = Gatuf_HTTP_URL_urlForView ('Registro_Views::dashboard');
				
				return new Gatuf_HTTP_Response_Redirect ($url);
			}
		} else {
			$form = new Registro_Form_Usuario_Actualizar (null, $extra);
		}
		
		return Gatuf_Shortcuts_RenderToResponse ('registro/user/actualizar.html',
		                                         array ('page_title' => 'Actualizar Perfil',
		                                                'form' => $form),
		                                         $request);
	}
}
