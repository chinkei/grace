<?php
$db['isReadToWrite'] = FALSE;
$db['group_server'] = array(
	'w' => array(
		0 => array(
			'host' => '127.0.0.1,127.0.0.1',
			'port' => 3306, 
			'name' => 'easymvc',       //修改数据
			'user' => 'root',		//修改数据用户名
			'pass' => 123456,     //修改数据用户名
		)
	),
	'r' => array(
	)
);
?>