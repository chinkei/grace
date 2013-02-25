<?php

/**
 * HMVC
 */
define('DEBUG', TRUE);

if (DEBUG === TRUE) {
	error_reporting(E_ALL);
    ini_set('display_errors','1');
} else {
	error_reporting(0);
	ini_set('display_errors', '0');
}

// 常量定义路径物理路径
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__));
define('HTML_PATH', ROOT_PATH.DS.'html');
define('APP_PATH', ROOT_PATH.DS.'app');
define('SYS_PATH', ROOT_PATH.DS.'hweo');

!defined('CLI') && define('CLI', FALSE);

require SYS_PATH.DS.'core/Common.php';
require SYS_PATH.DS.'Application.php';
Application::run();
?>