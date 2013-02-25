<?php
uses('Grace_Database_Db');
uses('Grace_Mvc_View_View');
uses('Grace_Mvc_View_Driver_Simple');
uses('Grace_Mvc_View_Driver_Template');

class Grace_Load_Load
{
	protected $_model    = array();
	protected $_library  = array();
	protected $_layout   = array();
	protected $_language = array();
	protected $_helper   = array();
	
	public function view($module, $driver = 'simple')
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
	public function model($name, $module)
	{
		$class = $module . '_' . $name . '_mdl';
		
		if ( !isset($this->_model[$class]) ) {
			require_once APP_PATH . '/module/' . $module  . '/model/' . $name . '.php';
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
	
	public function helper()
	{
		
	}
}
?>