<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

uses('Grace_Configure_Interface');
uses('Grace_Configure_Exception');

/**
 * 文件解析驱动类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Config
 */
class Grace_Configure_Driver
{
	protected static $_handle = array();
	
	public static function build($path, $type = 'php')
	{
		if ( !isset(self::$_handle[$type]) ) {
			$object = NULL;
		
			switch ($type) {
				case 'php':
					uses('Grace_Configure_Driver_Php');
					$object = new Grace_Configure_Driver_Php();
				break;
				
				case 'ini':
					uses('Grace_Configure_Driver_Ini');
					$object = new Grace_Configure_Driver_Ini();
				break;
				
				case 'json':
					uses('Grace_Configure_Driver_Json');
					$object = new Grace_Configure_Driver_Json();
				break;
				
				case 'yaml':
					uses('Grace_Configure_Driver_Xml');
					$object = new Grace_Configure_Driver_Yaml();
				break;
				
				default:
					throw new Grace_Configure_Exception(_vf('Could not use Configure type: %s', $type));
				break;
			}
			self::$_handle[$type] = $object;
		}
		
		self::$_handle[$type]->setPath($path);
		return self::$_handle[$type];
	}
}
?>