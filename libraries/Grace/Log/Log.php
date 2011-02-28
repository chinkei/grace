<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 日志记录类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Log
 */
class Grace_Log_Log
{
	const OFF       = 0;
    const EMERGENCY = 1;
	const ALERT     = 2;
	const CRITICAL  = 3;
	const ERROR     = 4;
	const WARNING   = 5;
	const NOTICE    = 6;
	const INFO      = 7;
	const DEBUG     = 8;
	const ALL       = 9;

	protected static $_instance = NULL;

	private $_handler = array();
	private $_level;

	/**
	 * 构造函数
	 * 
	 * @return void
	 */
	private function __construct()
	{
		$this->setLevel(self::WARN);
	}
	
	/**
	 * 禁止克隆对象
	 */
	private function __clone(){}
	
	/**
	 * 获取单例对象
	 * 
	 * @return object
	 */
	public static function getInstance()
	{
		if(self::$_instance === NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * 设置日志记录句柄对象
	 * 
	 * @param  Grace_Log_Interface
	 * @return void
	 */
	public function setHandler(Grace_Log_Interface $handler)
	{
		$this->_handler = array($handler);
	}
	
	/**
	 * 增加日志记录句柄对象
	 * 
	 * @param  Grace_Log_Interface
	 * @return void
	 */
	public function addHandler(Grace_Log_Interface $handler)
	{
		$this->_handler[] = $handler;
	}

	/**
	 * 清除句柄对象
	 * 
	 * @return void
	 */
	public function clearHandler()
	{
		$this->_handler = array();
	}

	/**
	 * 设置日志记录级别
	 * 
	 * @return void
	 */
	public function setLevel($level)
	{
		$this->level = (int)$level;
	}

	/**
	 * 获取日志记录级别
	 * 
	 * @return int
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * 记录日志
	 * 
	 * @param  int    $level 日志级别
	 * @param  string $msg   内容
	 * @return void
	 */
	public function addMessage($level, $msg)
	{
		if($level <= $this->getLevel()) {
			foreach($this->handler as $handler) {
				$handler->write(self::level($level), $msg);
			}
		}
	}

	/**
	 * 记录debug信息
	 * 
	 * @param string $msg
	 */
	public static function debug($msg)
	{
		self::getInstance()->addMessage(self::DEBUG, $msg);
	}

	public static function info($msg)
	{
		self::getInstance()->addMessage(self::INFO, $msg);
	}

	public static function notice($msg)
	{
		self::getInstance()->addMessage(self::NOTICE, $msg);
	}

	public static function warning($msg)
	{
		self::getInstance()->addMessage(self::WARNING, $msg);
	}

	public static function error($msg)
	{
		self::getInstance()->addMessage(self::ERROR, $msg);
	}
	
	public static function critical($msg)
	{
		self::getInstance()->addMessage(self::CRITICAL, $msg);
	}

	public static function alert($msg)
	{
		self::getInstance()->addMessage(self::ALERT, $msg);
	}

	public static function emergency($msg)
	{
		self::getInstance()->addMessage(self::EMERGENCY, $msg);
	}
	
	public static function levelName($level = FALSE)
	{
		$levels = array(
			self::DEBUG     => 'DEBUG',
	        self::INFO      => 'INFO',
	        self::NOTICE    => 'NOTICE',
	        self::WARNING   => 'WARNING',
	        self::ERROR     => 'ERROR',
	        self::CRITICAL  => 'CRITICAL',
	       	self::ALERT     => 'ALERT',
	        self::EMERGENCY => 'EMERGENCY',
		);
		return isset($levels[$level]) ? $levels[$level] : 'UNKNOWN';
	}
}
?>