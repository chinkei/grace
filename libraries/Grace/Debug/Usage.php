<?php  if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 内存使用类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Debug
 */
class Grace_Usage
{
	/**
	 * 返回内存使用量
	 * 
	 * @return string
	 */
	public static function memory()
	{
		get_instance()->load->helper('File');
		$bytes = memory_get_usage();
		
		return size_format($bytes);
	}
}
?>