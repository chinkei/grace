<?php
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
	
	abstract function set($key, $val, $time = 0);
	abstract function get($key);
	abstract function rm($key, $time = 0);
	
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
}
?>