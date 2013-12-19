<?php

function Calif_Migrations_Install_setup ($params=null) {
	$models = array ('Calif_Carrera',
	                 );
	$db = Gatuf::db ();
	$schema = new Gatuf_DB_Schema ($db);
	foreach ($models as $model) {
		$schema->model = new $model ();
		$schema->createTables ();
	}
	
	foreach ($models as $model) {
		$schema->model = new $model ();
		$schema->createConstraints ();
	}
	
	Calif_Migrations_Install_4Carreras_setup ();
}

function Calif_Migrations_Install_teardown ($params=null) {
	$models = array ('Calif_Carrera',
	                 );
	
	$db = Gatuf::db ();
	$schema = new Gatuf_DB_Schema ($db);
	
	foreach ($models as $model) {
		$schema->model = new $model ();
		$schema->dropConstraints();
	}
	
	foreach ($models as $model) {
		$schema->model = new $model ();
		$schema->dropTables ();
	}
}

function Calif_Migrations_Install_4Carreras_setup ($params = null) {
	$carrera_model = new Calif_Carrera ();
	
	$carreras = array ('BIM' => 'Ingeniería en Biomédica',
	                   'CEL' => 'Ingeniería en Electrónica y Comunicaciones',
	                   'CIV' => 'Ingeniería Civil',
	                   'COM' => 'Ingenieria en Computación',
	                   'FIS' => 'Licenciatura en Física',
	                   'INBI' => 'Ingeniería en Biomédica (Nueva)',
	                   'INCE' => 'Ingeniería en Electrónica y Comunicaciones (Nueva)',
	                   'INCO' => 'Ingeniería en Computación (Nueva)',
	                   'IND' => 'Ingeniería Industrial',
	                   'INF' => 'Licenciatura en Informática',
	                   'INNI' => 'Ingeniería en Informática (Nueva)',
	                   'IQU' => 'Ingeniería Química',
	                   'LIFI' => 'Licenciatura en Física (Nueva)',
	                   'LIMA' => 'Licenciatura en Matemáticas (Nueva)',
	                   'MAT' => 'Licenciatura en Matemáticas',
	                   'MEL' => 'Ingeniería Mecánica Eléctrica',
	                   'QFB' => 'Licenciatura en Químico Farmacobiólogo',
	                   'QUI' => 'Licenciatura en Química',
	                   'TOP' => 'Ingeniería en Topografía');
	
	foreach ($carreras as $clave => $descripcion) {
		$carrera_model->clave = $clave;
		$carrera_model->descripcion = $descripcion;
		$carrera_model->color = 0;
		
		$carrera_model->create (); /* NO raw para que los permisos se creen automáticamente */
	}
}

