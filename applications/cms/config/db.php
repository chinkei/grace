<?php

$db['default']['driver']       = 'mysql';
$db['default']['master_slave'] = FALSE;
$db['default']['use_weight']   = FALSE;
$db['default']['char_set']     = 'utf8';
$db['default']['db_prefix']    = 'sms_';

$db['default']['master'] = array(
	'hostname' => '10.1.16.137',
	'username' => 'root',
	'password' => 'chenjia',
	'database' => 'esocc_sms',
	'dbport' => '3306',
);

$db['default']['slave'] = array(
	array(
		'hostname' => '10.1.16.137',
		'username' => 'root',
		'password' => 'chenjia',
		'database' => 'php',
		'dbport' => '3306',
		'weight' => '50'
	),
	array(
		'hostname' => '10.1.16.137',
		'username' => 'root',
		'password' => 'chenjia',
		'database' => 'test',
		'dbport' => '3306',
		'weight' => '100'
	),
	array(
		'hostname' => '10.1.16.137',
		'username' => 'root',
		'password' => 'chenjia',
		'database' => 'soc_esocc',
		'dbport' => '3306',
		'weight' => '200'
	)
);

return $db;
?>