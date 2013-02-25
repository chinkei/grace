<?php
$driver  = 'file';

$cache['file'] = array(
	'prefix'     => 'grace_file_',
	//'cache_path' => ''
);

$cache['apc'] = array(
	'prefix' => 'grace_apc_'
);

$cache['memcached'] = array(
	'prefix'     => 'grace_apc_',
	'server' => array(
		array('host' => '10.200.53.64', 'port' => 11211, 'weight' => 100),
	)
);
?>