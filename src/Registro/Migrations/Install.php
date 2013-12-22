<?php

function Registro_Migrations_Install_setup ($params = null) {
	$models = array ('Registro_Curso');
	
	$db = Gatuf::db ();
	
	$schema = new Gatuf_DB_Schema($db);
	foreach ($models as $model) {
		$schema->model = new $model();
		$schema->createTables();
	}
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
