<?php
$path = '';
class Cache_File implements ifc_cache
{
	public function __construct()
	{
		
	}
	
	public function set($key, $value, $expires = 0)
	{
		$data = '<?php if ( ! defined("IN_CACHE") )  exit("Access denied!");' . "\n//" 
				. sprintf("%012d", $expire) . "\n" . serialize($value) .  "\n?>";
		
		$file = $this->_getName();
		
		if (FALSE !== file_put_contents($file, $data)) {
			clearstatcache();
			return TRUE;
		}
		return FALSE;
	}
	
	public function get($key)
	{
		$file = $this->_getName();
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
	
	protected function _getName($key)
	{
		global $path;
		return $path.'cache_'.md5($key).'.php';
	}
	
	protected function _read($file)
	{
		if ( ! $handle = fopen($file, 'rb') ) {
			return FALSE;
		}
		
		// 读取<?php
		fgets($handle);
		// 读取有效时间
		$strTime = substr(fgets($handle), 2);
		
		if ( ! is_numeric($strTime) ) {
			return FALSE;
		}
		
		$data = '';
		while ( ! feof($handle) ) {
			$data .= fgets($handle, 4096);
		}
		fclose($handle);
		return substr($data, 0, -3);
	}
}
?>