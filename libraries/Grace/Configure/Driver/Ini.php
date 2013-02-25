<?php
class Grace_Configure_Driver_Ini implements Grace_Configure_Interface
{
	protected $_path = '';
	
	public function setPath($path)
	{
		$this->_path = rtrim($path, '/') . '/';
	}
	
	public function read($key)
	{
		$fileName = rtrim($this->_path, '/') . '/' . $key . '.ini';
		
        if ( !file_exists($fileName) ) {
        	throw new Grace_Configure_Exception(_vf('Could not load Config file: %s', $fileName));
        }
		
        return parse_ini_file($fileName, TRUE);
	}
}
?>