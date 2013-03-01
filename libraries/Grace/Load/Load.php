<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

uses('Grace_Database_Db');
uses('Grace_Mvc_View_View');
uses('Grace_Mvc_View_Driver_Simple');
uses('Grace_Mvc_View_Driver_Template');

/**
 * 加载类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Load
 */
class Grace_Load_Load
{
	protected $_model    = array();
	protected $_library  = array();
	protected $_layout   = array();
	protected $_language = array();
	protected $_helper   = array();
	
	public function view($driver = 'simple', $module = FALSE)
	{
		$driver = strtolower($driver);
		
		$view   = NULL;
		switch ($driver) {
			case 'simple':
				$view = new Grace_Mvc_View_Driver_Simple($module);
			break;
			case 'template':
				$view = new Grace_Mvc_View_Driver_Template($module);
			break;
			
			default:
			break;
		}
		
		if ($view instanceof Grace_Mvc_View_Interface) {
			return new Grace_Mvc_View_View($view);
		}
		return FALSE;
	}
	
	public function database($param = '')
	{
		return Grace_Database_Db::loadDriver($param);
	}
	
	/**
	 * 载入Model处理类
	 */
	public function model($name, $module = FALSE)
	{
		$class = ( $module !== FALSE ) ? $module . '_' . $name . '_mdl' : $name . '_mdl';
		
		if ( !isset($this->_model[$class]) ) {
			$modelFile = APP_PATH . ( $module !== FALSE ? '/module/' . $module : '' ) . '/model/' . $name . '.php';
			require_once $modelFile;
			
			$this->_model[$class] = new $class;
		}
		
		return $this->_model[$class];
	}
	
	/**
	 * 载入类库文件
	 */
	public function library($library, $args = array())
	{
		$class = $library;
		
		if ( !isset($this->_library[$class]) ) {
			include_once APP_PATH . '/library/' . $library . '.php';
			
			if ( is_array($args) && !empty($args) ) {
				$reflection = new ReflectionClass($class);
				$object     = $reflection->newInstanceArgs($args);
			} else {
				$object = new $class;
			}
			$this->_library[$class] = $object;
		}
		return $this->_library[$class];
	}
	
	/**
	 * 载入布局类
	 * 
	 * @param  string      $layout 布局名称
	 * @param  bool|string $module 模块名
	 * @return object
	 */
	public function layout($layout)
	{
		$class = $layout . '_lay';
		
		if ( !isset($this->_layout[$class]) ) {
			require_once APP_PATH . '/layout/' . $layout . '.php';
			$this->_layout[$class] = new $class;
		}
		return $this->_layout[$class];
	}
	
	public function language()
	{
		
	}
	
	public function helper($file, $base = LIB_PATH)
	{
		$base = rtrim($base, '/');
		
		if ($base == LIB_PATH) {
			$file = ucfirst($file);
			$base = $base . '/Grace/Helper';
		} else {
			$base = $base . '/helper';
		}
		
		import_file($file, TRUE, $base);
	}
}
?>