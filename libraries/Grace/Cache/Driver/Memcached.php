<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * memcached缓存类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Cache
 */
class Grace_Cache_Driver_Memcached extends Grace_Cache_Cache
{
	private $_memcached = NULL;
	
	public function __construct($settings)
	{
		if ( !isset($settings['server']) ) {
			trigger_error('Memcache Server is not exists');
			exit();
		}
		
		$memcached = new Memcached;
		
		$servers = $settings['server'];
		if ( count($servers) > 1 ) {
			call_user_func(array($memcached, 'addServers'), $servers);
		} else {
			call_user_func_array(array($memcached, 'addServer'), $servers[0]);
		}
		
		if (isset($settings['prefix'])) {
			$this->_keyPrefix = $settings['prefix'];
		}
		
		$this->_memcached = $memcached;
		
	}
	
	public function set($key, $val, $expire = 0)
	{
		$key = $this->_getKey($key);
		$this->_memcached->set($key, $val, $expire);
	}
	
	public function get($key)
	{
		$key = $this->_getKey($key);
		return $this->_memcached->get($key);
	}
	
	public function rm($key, $timeout = 0)
	{
		$key = $this->_getKey($key);
		return $this->_memcached->delete($key, $timeout);
	}
	
    public function add($key, $val, $expire = 0)
	{
		$key = $this->_getKey($key);
    	return $this->_memcached->add($key, $val, $expire);
    }
	
	/**
     * 替换已经存在的元素的值
     */
    public function replace($key, $val, $expire = 0)
	{
		$key = $this->_getKey($key);
    	return $this->_memcached->replace($key, $val, 0, $expire);
    }
}
?>