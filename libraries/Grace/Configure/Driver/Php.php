<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * php配置文件解析类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Config
 */
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