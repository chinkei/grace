<?php
class Grace_Cache_Driver_Redis extends Grace_Cache_Cache
{
	public function __construct($settings)
	{
		if (isset($settings['prefix'])) {
			$this->_keyPrefix = $settings['prefix'];
		}
	}
	
	public function set($key, $val, $time = 0)
	{
		
	}
	
	public function get($key)
	{
		
	}
	
	public function rm($key)
	{
		
	}
}
?>