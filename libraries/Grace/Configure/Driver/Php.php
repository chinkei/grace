<?php
class Grace_Configure_Driver_Php implements Grace_Configure_Interface
{
	protected $_path = '';
	
	public function setPath($path)
	{
		$this->_path = rtrim($path, '/') . '/';
	}
	
	public function read($key)
	{
		$fileName = $this->_path . $key . '.php';
		
		if ( !file_exists($fileName) ) {
        	throw new Grace_Configure_Exception(_vf('Could not load Config file: %s', $fileName));
        }
		return include $fileName;
	}
}
?>