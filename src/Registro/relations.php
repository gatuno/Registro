<?php

$m = array ();

$m['Registro_Curso'] = array ('relate_to' => array ('Registro_User'),
                              'relate_many' => array ('Registro_User'));

return $m;
