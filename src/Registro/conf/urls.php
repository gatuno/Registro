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
	'regex' => '#^/curso/add/$#',
	'base' => $base,
	'model' => 'Registro_Views_Curso',
	'method' => 'agregar'
);

return $ctl;
