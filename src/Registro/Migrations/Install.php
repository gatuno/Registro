<?php

function Registro_Migrations_Install_setup ($params = null) {
	$models = array ('Registro_Curso');
	
	$db = Gatuf::db ();
	
	$schema = new Gatuf_DB_Schema($db);
	foreach ($models as $model) {
		$schema->model = new $model();
		$schema->createTables();
	}
	
	Registro_Migrations_Install_extraCarreras ();
}

function Registro_Migrations_Install_teardown ($params = null) {
	$models = array ('Registro_Curso');
	
	$models = array_reverse ($models);
	$db = Gatuf::db ();
	
	$schema = new Gatuf_DB_Schema($db);
	foreach ($models as $model) {
		$schema->model = new $model();
		$schema->dropTables();
	}
}

function Registro_Migrations_Install_extraCarreras ($params = null) {
	$carrera_model = new Calif_Carrera ();
	
	$carreras = array ('A1' => 'Otra carrera',
	                   'A2' => 'Bachillerato General',
	                   'A3' => 'Bachillerato Técnico',
	                   'A4' => 'Secundaria');
	
	foreach ($carreras as $clave => $descripcion) {
		$carrera_model->clave = $clave;
		$carrera_model->descripcion = $descripcion;
		$carrera_model->color = 0;
		
		$carrera_model->create (); /* NO raw para que los permisos se creen automáticamente */
	}
}
