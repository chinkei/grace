<?php  if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 时间运行记录类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Debug
 */
class Grace_Timer
{
	
	/**
	 * 开始时间
	 * 
	 * @static
	 */
	protected static $_startTime = 0;
	
	/**
	 * 结束时间
	 * 
	 * @static
	 */
	protected static $_endTime   = 0;
	
	/**
	 * 记录开始时间
	 * 
	 * @return void
	 */
	public static function reckonStart()
	{
		self::$_startTime = self::_getMicrotime();
	}
	
	/**
	 * 获取记录时长
	 *
	 * @return float 秒
	 */
	public static function getRunTimes()
	{
		self::_reckonEnd();
		return round((self::$_endTime - self::$_startTime), 4);
	}
	
	/**
	 * 记录结束时间
	 *
	 * @return void
	 */
	private static function _reckonEnd()
	{
		self::$_endTime = self::_getMicrotime();	
	}
	
	/**
	 * 记录结束时间
	 *
	 * @return float 秒
	 */
	private static function _getMicrotime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return (float)$usec + (float)$sec;
	}
}
?>