<?php
class GR_Config
{
	
	/**
	 * 配置信息
	 * 
	 * @var Array
	 */
	protected $configs = array();
	
	/**
	 * 加载信息
	 * 
	 * @var Array
	 */
	protected $isLoaded = array();
	
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		// 加载框架配置文件
		$file_path = APP_PATH.'config/config.php';
		if ( ! file_exists($file_path)) {
			exit('The configuration file does not exist.');
		}
		
		require($file_path);
		
		if ( ! isset($config) || ! is_array($config)) {
			exit('Your config file does not appear to be formatted correctly.');
		}
		
		$this->configs = $config;
		unset($config);
	}
	
	public function get_config()
	{
		print_r($this->configs);
	}
	
	/**
	 * 载入文件
	 * 
	 * @param  String $filename 文件名
	 * @param  String $module   模块名
	 * @return Bool
	 */
	public function load($filename, $module = '') 
	{
		$file = '';
		if ($module == '') {
			$file = 'config/' . $filename . '.php';
		} else {
			$file = 'modules/' . $module . '/config/' . $filename . '.php';
		}
		
		if (in_array($file, $this->isLoaded, TRUE)) {
			return TRUE;
		}
		
		if ( ! file_exists(APP_PATH . $file)) {
			exit('Your '.$file.' file does not exists');
		}
		
		include(APP_PATH . $file);
		
		if ( ! isset($$filename) || ! is_array($$filename)) {
			trigger_error('Your '.$file.' file does not appear to contain a valid configuration array.', E_USER_ERROR);
		}
		
		$this->configs[$filename] = $$filename;
		$this->isLoaded[] = $file;
		unset($$filename);
		
		return TRUE;
  	}
	
	/**
	 * 设置配置项
	 * 
	 * @param String $item   配置项名
	 * @param String $val    配置项值
	 * @param String $module 模块名
	 */
	public function set_item($item, $val, $module = '')
	{
		$ref =& $this->configs;
		if ($module != '') {
			if ( ! isset($this->configs[$module]) ) {
				return FALSE;
			}
			$ref =& $this->configs[$module];
		}
		
		$indexs = explode('.', $item);
		foreach ($indexs as $index) {
			if (is_array($ref) && array_key_exists($index, $ref)) {
				$ref =& $ref[$index];
			} else {
				return FALSE;
			}
		}
		$ref = $val;
	}
	
	/**
	 * 获取配置项值
	 * 
	 * @param  String $item 配置项名
	 * @param  String $module 模块名
	 * @reutrn Mixed
	 */
	public function get_item($item, $module = '') 
	{
		$ref =& $this->configs;
		if ($module != '') {
			if ( ! isset($this->configs[$module]) ) {
				return FALSE;
			}
			$ref =& $this->configs[$module];
		}
		
		$indexs = explode('.', $item);
		foreach ($indexs as $index) {
			if (is_array($ref) && array_key_exists($index, $ref)) {
				$ref =& $ref[$index];
			} else {
				return FALSE;
			}
		}
		return $ref;
  	}
}
?>