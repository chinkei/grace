<?php
/**
 * HMVC
 */
 
// 当前项目名
define('APP_NAME', 'CMS');

$dir = dirname(__FILE__);
define('ROOT_PATH', substr($dir, 0, -15) );

define('APP_PATH', ROOT_PATH . '/applications/' . APP_NAME);

define('LIB_PATH', ROOT_PATH.'/libraries');

!defined('CLI') && define('CLI', FALSE);


require LIB_PATH . '/Grace/Application/Application.php';

Grace_Application_Application::run(APP_NAME);
?>