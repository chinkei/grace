<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * json配置文件解析类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Config
 */
class Grace_Configure_Driver_Json implements Grace_Configure_Interface
{

	/**
     * The path to read ini files from.
     *
     * @var array
     */
    protected $_path;
	
	public function setPath($path)
	{
		$this->_path = rtrim($path, '/') . '/';
	}

    /**
     * Read an ini file and return the results as an array.
     *
     * @param string $file Name of the file to read. The chosen file
     *    must be on the reader's path.
     * @return array
     * @throws ConfigureureException
     */
    public function read($key)
    {
        $fileName = $this->_path . $key . '.json';

		if ( !file_exists($fileName) ) {
        	throw new Grace_Configure_Exception(_vf('Could not load Config file: %s', $fileName));
        }

        $json = file_get_contents($file);
        return json_decode($json);
    }
}
?>