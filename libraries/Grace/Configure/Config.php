<?php
class Grace_Configure_Config
{
	/**
	 * 读取配置文件的句柄对象
	 *
	 * @var Grace_Config_Interface
	 */
	private $_handle = NULL;
	
	/**
	 * 配置项数组
	 *
	 * @var array
	 */
	private $_items  = array();
	
	/**
	 * 构造函数
	 *
	 * @param  Grace_Config_Interface $handle 句柄对象
	 * @return void
	 */
	public function __construct(Grace_Configure_Interface $handle, $key)
	{
		$this->_handle = $handle;
		$this->_items  = $this->_handle->read($key);
	}
	
	/**
	 * 获取配置项值
	 * 
	 * @param  string     $key 键值a.b.c LIKE $conf['a']['b']['c']
	 * @return bool|mixed 配置项值
	 */
	public function get($key = NULL)
	{
		if ($key === NULL) {
			return $this->_items;
		}
		
		$ref =& $this->_items;
		
		foreach (explode('.', $key) as $segment) {
			if ( is_array($ref) && array_key_exists($segment, $ref)) {
				$ref =& $ref[$segment];
			} else {
				return FALSE;
			}
		}
		
		return $ref;
	}
	
	/**
	 * 设置配置项值
	 * 
	 * @param  string $key 键值a.b.c LIKE $conf['a']['b']['c']
	 * @return void
	 */
	public function set($key, $val)
	{
		$ref  =& $this->_items;
		$keys = explode('.', $key);
		
		while (count($keys) > 1) {
			$key = array_shift($keys);
			
			// 如果不存在或不是数组则赋值一个空数组
			if ( ! isset($ref[$key]) || ! is_array($ref[$key])) {
				$ref[$key] = array();
			}
			$ref =& $ref[$key];
		}
		
		$ref[array_shift($keys)] = $val;
	}
	
	/**
	 * 替换配置项值
	 * 
	 * @param  string $key 键值a.b.c LIKE $conf['a']['b']['c']
	 * @return mixed|bool FALSE: 替换失败,不存在该配置项
	 */
	public function replace($key, $val)
	{
		$ref =& $this->_items;
		
		foreach (explode('.', $key) as $segment) {
			if ( is_array($ref) && array_key_exists($segment, $ref)) {
				$ref =& $ref[$segment];
			} else {
				return FALSE;
			}
		}
		
		$ref = $val;
	}
	
	/**
	 * 检查配置项值是否存在
	 * 
	 * @param  string $key 键值a.b.c LIKE $conf['a']['b']['c']
	 * @return bool
	 */
	public function has($key)
	{
		if ( !$key ) {
			return FALSE;
		}
		
		$ret = $this->get($key);
		return $ret === FALSE ? FALSE : TRUE; 
	}
}
?>