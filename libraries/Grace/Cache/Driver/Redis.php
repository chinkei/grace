<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * redies缓存类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Cache
 */
class Grace_Cache_Driver_Redis extends Grace_Cache_Cache
{
	protected $_redis = NULL;
	
	public function __construct($settings)
	{
		if (isset($settings['prefix'])) {
			$this->_keyPrefix = $settings['prefix'];
		}
		$this->_redis = new Redis;
	}
	
	public function set($key, $val, $time = 0)
	{
		$this->forever($key, $val);
	}
	
	public function get($key)
	{
		
	}
	
	public function rm($key)
	{
		
	}
	
	public function forever($key, $value)
	{
		$key = $this->_getKey();
		$this->_redis->set($key, serialize($value));
	}
}
?>