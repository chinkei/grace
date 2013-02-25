<?php
$root = dirname(__FILE__);
define('APP_PATH', $root .'/../app/');
require "../hweo/core/config.php";
$config = new Config();
$config->load('config');
$config->load('test', 'home');
$config->set_item('config.ceshi.a', 'afaf');
echo $config->get_item('config.ceshi.a');
$config->get_config();
?>