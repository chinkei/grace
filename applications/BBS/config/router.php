<?php

define('PARM_INT', 0);

define('PARM_FLOAT', 1);

define('PARM_UUID', 2);

define('PARM_REG_MATCH', 3);

define('PARM_REG_REPLACE', 4);

$default['/:module/:control/:method']

$router['/:id-:name/:delete'] = array(
	'id'     => PARM_INT,
	'name'   => PARM_INT,
	'delete' => PARM_INT,
);
?>