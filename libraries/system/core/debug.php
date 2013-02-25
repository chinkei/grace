<?php
class GR_Debug
{
	private static $startTime = 0;
	
	public static function run()
	{
		self::useMemory();
		self::exeTime();
	}
	
	/**
	 * 设置程序开始执行时间
	 */
	public static function appStart()
	{
		self::$startTime = micro_time();
	}
	
	/**
	 * 内存使用量
	 */
	public static function useMemory()
	{
		echo '<br />内存使用：'.byte_convert(memory_get_usage());
	}
	
	/**
	 * 程序执行时间
	 */
	public static function exeTime()
	{
		$exeTime = micro_time() - self::$startTime;
		echo '<br />执行时间：'.$exeTime.'s';
	}
	
	/**
	 * 分段运行时间
	 */
	public static function  runTime() {
		$exeTime = micro_time() - self::$startTime;
		$debug = debug_backtrace();
		
		//打印代码执行的行数.以及执行花费时间.
		echo  $debug[0]['file'].'|'.$debug[0] ['line'] . '|' . $exeTime . '<br />';
	}
}
?>