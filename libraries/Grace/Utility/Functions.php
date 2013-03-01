<?php
if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 实现命名空间功能
 *
 * @param  string $className 类名
 * @param  string $location  地址前缀
 * @return void
 */
function uses($className, $location = LIB_PATH)
{
	Grace_Application_Application::uses($className, $location);
}

/**
 * 获取类对象
 * 
 * @param  string $class 类名
 * @param  bool   $isRequire 引入方式 require/include
 * @param  string $base 路径前缀
 * @return object
 */
function load_class($class, $isRequire = TRUE, $base = LIB_PATH)
{
	static $_load_class = array();
	
	if ( ! isset($_load_class[$class]) ) {
	
		if ( FALSE === import_class($class, $isRequire, $base) ) {
			return FALSE;
		}
		
		$_instace = new $class;
		is_object($_instace) && $_load_class[$class] = $_instace;
	}
	
	return $_load_class[$class];
}

/**
 * 引入文件
 * TODO   该方法只适用于后缀.php的文件(不适用.class.php, .inc.php后缀文件)
 * 
 * @param  string $path       路径符以'.'号隔开,不包含后缀 如a.b.c Like a/b/c.php
 * @param  bool   $isRequire  true: require(), false:include();
 * @param  string $base       根目录(可以是框架路径,也可以是项目路径)
 * @return mixed
 */
function import_file($path, $isRequire = TRUE, $base = LIB_PATH)
{
	static $is_imported = array();
	
	$fileName = rtrim($base, '/') . '/' . str_replace('.', '/', $path);
	if ( !in_array($fileName, $is_imported, TRUE) ) {
	
		$fileFullPath = $fileName . '.php';
		
		if (file_exists($fileFullPath)) {
			$is_imported[] = $fileName;
			return $isRequire ? require ($fileFullPath) : include ($fileFullPath);
		}
	}
	return FALSE;
}

/**
 * 载入类文件
 * 
 * @param  string $class 类名
 * @param  bool   $isRequire 引入方式 require/include
 * @param  string $base 路径前缀
 * @return bool
 */
function import_class($class, $isRequire = TRUE, $base = LIB_PATH)
{
	$strPath = trim($class, '_');
	$filePath = $base . '/' . str_replace('_', '/', $strPath) . '.php';
	
	if ( ! file_exists($filePath) ) {
		throw new Exception('file : "' . str_replace('_', '/', $strPath) . '" is not exists');
	}
	return ( $isRequire === TRUE ) ? require $filePath : include $filePath;
}

/**
 * 格式化字符串
 * 
 * @param  string $value 要格式化的字符串
 * @param  array  $args  参数数组
 * @return string
 */
function _vf($value, $args = NULL)
{
	if ( !$value ) {
		return NULL;
	}
	
	if ($args === NULL) {
		return $value;
	}
	
	if ( !is_array($args) ) {
		$args = array_slice(func_get_args(), 1);
	}
	return vsprintf($value, $args);
	
}

/**
 * 载入布局视图
 * 
 * @param  array $args 参数
 * @return void
 */
function load_layout($args = array())
{
	$counts = count($args);
	if ($counts < 2) {
		return FALSE;
	}
	
	$layout = Grace_Ioc_Ioc::resolve('layout');
	call_user_func_array(array($layout, 'render'), $args);
}

/**
 * 获取当前控制器对象实例
 * 
 * @return object
 */
function get_instance()
{
	uses('Grace_Mvc_Contrl_Controller');
	return Grace_Mvc_Contrl_Controller::getInstance();
}
?>