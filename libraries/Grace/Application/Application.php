<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/
 
/**
 * 项目框架启动类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Application
 */
class Grace_Application_Application
{
	/**
     * 类映射数组
	 * 
     * @var array
     */
    private static $_classMap = array();
	
	/**
	 * 项目是否运行
	 * 
	 * @var array 
	 */
	private static $_isRun = array();
	
	/**
	 * 启动项目
	 * 
	 * @param  string    $appName 应用名称
	 * @return bool|void
	 */
	public static function run($appName = '')
	{
		if (isset(self::$_isRun[$appName]) && self::$_isRun[$appName] === TRUE) {
			return TRUE;
		}
		require APP_PATH . '/Bootstrap.php';
	}
	
	/**
	 * 框架初始化
	 * 
	 * return void
	 */
	public static function init()
	{
		self::_initSystem();
		self::_initDependence();
		self::_initApp();
	}
	
	public static function uses($className, $location)
	{
		self::$_classMap[$className] = $location;
	}
	
	/**
	 * 初始化依赖模块
	 * 
	 * @return void
	 */
	protected static function _initDependence()
	{
		// 载入公共方法
		require LIB_PATH . '/Grace/Utility/Functions.php';
		
		// 引入注入容器类
		uses('Grace_Ioc_Ioc');
		uses('Grace_Configure_Driver');
		
		Grace_Ioc_Ioc::register('event', array('class' => 'Grace_Event_Event'), TRUE);
		Grace_Ioc_Ioc::register('routing', array('class' => 'Grace_Routing_Mapper'), TRUE);
		Grace_Ioc_Ioc::register('filter', array('class' => 'Grace_Filter_Filter'), TRUE);
		Grace_Ioc_Ioc::register('input', array('class' => 'Grace_IO_Input'), TRUE);
		Grace_Ioc_Ioc::register('output', array('class' => 'Grace_IO_Output'), TRUE);
		Grace_Ioc_Ioc::register('load', array('class' => 'Grace_Load_Load'), TRUE);
		Grace_Ioc_Ioc::register('layout', array('class' => 'Grace_Mvc_Layout_Layout'), TRUE);

		// 载入全局配置类
		$conf_handle = Grace_Configure_Driver::build(APP_PATH.'/config', 'php');
		Grace_Ioc_Ioc::register('config', array('class' => 'Grace_Configure_Config', 'params' => array($conf_handle, 'config')), TRUE);
	}

	/**
	 * 框架初始化
	 * 
	 * @return void
	 */
	protected static function _initSystem()
	{
		// 错误最高级别
		error_reporting(E_ALL);
		
		set_error_handler(array('Grace_Application_Application', '_error'));         // 自定义PHP错处处理
		spl_autoload_register(array('Grace_Application_Application', '_autoload'));  // 初始化自动装载器
		set_exception_handler(array('Grace_Application_Application', '_exception')); // 异常处理
		
		// 检测框架是否在 CLI 模式下运行 (PHP命令行模式)
		//QP_Sys::_checkSapi();
	}
	
	/**
	 * 项目初始化
	 * 
	 * @return void
	 */
	protected static function _initApp()
	{
		$output = Grace_Ioc_Ioc::resolve('output');
		$output->startBuffer();
		$output->setContentType('html');
		
		self::_dispatch();
		
		$output->endBuffer();
	}
	
	/**
	 * 执行调配器
	 *
	 * @return void
	 */
	protected static function _dispatch()
	{
		uses('Grace_Mvc_Dispatcher');
		$dispatcher = new Grace_Mvc_Dispatcher();
		$dispatcher->execute();
	}
	
	/**
	 * 自定义错误处理
	 */
	public static function _error($errno , $errstr, $errfile, $errline, $errcontext)
	{
		echo $errstr . "   $errfile=>$errline";
	}
	
	/**
	 * 自动加载类文件
	 * 
	 * @param  string $className 类名
	 * @return bool
	 */
	public static function _autoload($className)
	{
		if ( ! isset(self::$_classMap[$className]) ) {
            return FALSE;
        }
		return import_class($className, FALSE, self::$_classMap[$className]);
	}
	
	/**
	 * 自定义异常处理
	 */
	public static function _exception($exception)
	{
		echo $exception->getMessage();
	}
}
?>