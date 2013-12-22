<?php

$m = array ();

$m['Registro_Curso'] = array ('relate_to_many' => array ('Registro_User'),
                              'relate_to' => array ('Registro_User'));

return $m;
