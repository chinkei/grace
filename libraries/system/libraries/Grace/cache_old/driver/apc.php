<?php
class Cache_Apc implements ifc_cache
{
	/**
	 * 构造函数
	 */
	public function __construct(){
		// 是否安装了扩展
		if(! extension_loaded('apc')){
			throw new QP_Exception('Apc 扩展没有安装.');
		}
	}
	
	public function set($key, $value, $expire = 0)
	{
		
	}
	
	public function get($key)
	{
		
	}
	
	public function delete($key)
	{
		
	}
	
	public function flush()
	{
		
	}
	
	public function clear()
	{
		
	}
}
?>