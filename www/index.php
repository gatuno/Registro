<?php

require dirname(__FILE__).'/../src/Registro/conf/path.php';

# Cargar Gatuf
require 'Gatuf.php';

# Inicializar las configuraciones
Gatuf::start(dirname(__FILE__).'/../src/Registro/conf/registro.php');

Gatuf_Despachador::loadControllers(Gatuf::config('registro_views'));

Gatuf_Despachador::despachar(Gatuf_HTTP_URL::getAction());
