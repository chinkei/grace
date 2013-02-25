<?php
define('CLI', FALSE);
require "../core/router.php";
$r = new Router();
echo $r->site_url('test_af/ceshi_ce/afg_af',  array('a'=>'1', 'b'=> '2'));
$r->init();

echo Router::getModule();
echo Router::getContrl();
echo Router::getMethod();
echo Router::getArgv();
echo Router::getBaseUrl();
?>