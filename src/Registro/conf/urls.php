<?php
$base = Gatuf::config('registro_base');
$ctl = array ();

/* Bloque base:
$ctl[] = array (
	'regex' => '#^/ /$#',
	'base' => $base,
	'model' => 'Registro_',
	'method' => '',
);
*/

/* Sistema de login, y vistas base */
$ctl[] = array (
	'regex' => '#^/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'index',
);

$ctl[] = array (
	'regex' => '#^/login/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'login',
	'name' => 'login_view'
);

$ctl[] = array (
	'regex' => '#^/logout/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'logout',
);

/* Recuperación de contraseñas */
$ctl[] = array (
	'regex' => '#^/password/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'passwordRecoveryAsk',
);

$ctl[] = array (
	'regex' => '#^/password/ik/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'passwordRecoveryInputCode',
);

$ctl[] = array (
	'regex' => '#^/password/k/(.*)/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'passwordRecovery',
);

$ctl[] = array (
	'regex' => '#^/register/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'register'
);

$ctl[] = array(
	'regex' => '#^/register/k/(.*)/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'registerConfirmation'
);

$ctl[] = array(
	'regex' => '#^/register/ik/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'registerInputKey'
);

$ctl[] = array (
	'regex' => '#^/dashboard/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'dashboard',
);

$ctl[] = array (
	'regex' => '#^/dashboard/update/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'actualizar',
);

$ctl[] = array (
	'regex' => '#^/admin/$#',
	'base' => $base,
	'model' => 'Registro_Views',
	'method' => 'siteAdmin',
);

$ctl[] = array (
	'regex' => '#^/cursos/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'index'
);

$ctl[] = array (
	'regex' => '#^/curso/add/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'agregar'
);

$ctl[] = array (
	'regex' => '#^/curso/(\d+)/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'verCurso'
);

$ctl[] = array (
	'regex' => '#^/curso/(\d+)/update/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'editar'
);

$ctl[] = array (
	'regex' => '#^/curso/(\d+)/matricular/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'matricular'
);

$ctl[] = array (
	'regex' => '#^/curso/(\d+)/desmatricular/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'desmatricular'
);

$ctl[] = array (
	'regex' => '#^/curso/(\d+)/lista/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'verLista'
);

$ctl[] = array (
	'regex' => '#^/curso/(\d+)/lista/ODS/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'descargarListaODS'
);

return $ctl;
