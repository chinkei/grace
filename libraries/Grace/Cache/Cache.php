<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 缓存抽象类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Cache
 */
abstract class Grace_Cache_Cache
{
	/**
	 * 键值前缀
	 * 
	 * @var string
	 */
	protected $_key_prefix = 'Grace_';
	
	/**
	 * 驱动对象数组
	 * 
	 * @var array
	 */
	protected static $_drivers  = array();
	
	public static function loadDriver($param = '')
	{
		$configHandle = Grace_Config_Driver::build(APP_PATH.'/config', 'php');
		$cache        = $configHandle->read('cache');
		
		if ( ! isset($cache) || count($cache) == 0) {
			show_error('No database connection settings were found in the database config file.');
		}
		
		if ($param != '') {
			$driver = $param;
		}
		
		if ( !isset(self::$_drivers[$driver]) ) {
			if ( !isset($cache[$driver]) ) {
				// TODO
				trigger_error('不存在这个数据模型', E_USER_ERROR);
			}
			$class = ucfirst($driver);
			include "Driver/{$class}.php";
			//import_file('libraries.database.connectors.driver.conn_'.$db[$driver]['dirver']);
			$dirverClass = 'Grace_Cache_Driver_' . ucfirst($driver);
			// 实例化对象
			self::$_drivers[$driver] = new $dirverClass($cache[$driver]);
		}
		return self::$_drivers[$driver];
	}
	
	/**
	 * 获取处理后的缓存键值
	 * 
	 * @param  string $key
	 * @return string 
	 */
	protected function _getKey($key)
	{
		return md5($this->_key_prefix.$key);
	}
	
	// ---------------------------------------------- 子类需要实现的方法 START -------------------------------------------- //
	
	/**
	 * 设置缓存值
	 * 
	 * @param string $key 键
	 * @param mixed  $val 值
	 */
	abstract public function set($key, $val, $time = 0);
	
	/**
	 * 获取缓存值
	 * 
	 * @param  stirng $key 健
	 * @return mixed 缓存值
	 */
	abstract public function get($key);
	
	/**
	 * 删除缓存值
	 * 
	 * @param string $key
	 * @param int    $time 延迟时间
	 */
	abstract public function rm($key, $time = 0);
	
	abstract public function has($key);
	
	abstract public function forever($key, $value);
	
	// ---------------------------------------------- 子类需要实现的方法  END  -------------------------------------------- //
}
?>