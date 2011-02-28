<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * Apc缓存类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Cache
 */
class Grace_Cache_Driver_Apc extends Grace_Cache_Cache
{

	/**
	 * 构造函数
	 * 
	 * @param  array $settings 配置项数组
	 * @return void
	 */
	public function __construct($settings)
	{
		if (isset($settings['prefix'])) {
			$this->_key_prefix = $settings['prefix'];
		}
	}
	
	/**
	 * 设置缓存值
	 *
	 * @param  string $key  键
	 * @param  mixed  $val  缓存值
	 * @param  int    $time 有效期
	 * @return void
	 */
	public function set($key, $val, $time = 0)
	{
		apc_store($this->_getKey($key), $val, $time);
	}
	
	public function get($key)
	{
		return apc_fetch($this->_getKey($key));
	}
	
	public function rm($key)
	{
		apc_delete($this->_getKey($key));
	}
	
	public function has($key)
	{
		return ( $this->get($this->_getKey($key)) === FALSE ) : FALSE : TRUE;
	}
}
?>