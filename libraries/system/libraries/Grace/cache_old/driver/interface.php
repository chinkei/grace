<?php
interface ifc_cache
{
	public function set($key, $value, $expire = 0);
	
	public function get($key);
	
	public function delete($key);
	
	public function clear();
	
	public function flush();
}
?>