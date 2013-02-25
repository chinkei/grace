<?php
class Db
{
	protected $conn_id   = FALSE;
	protected $result_id = FALSE;
	protected $pconnect  = FALSE;
	
	protected $dbdriver		= 'mysql';
	protected $dbprefix		= '';
	protected $char_set		= 'utf8';
	protected $dbcollat		= 'utf8_general_ci';
	protected $error    = '';
	protected $dbName   = '';
	
	protected $buildSql = 'SELECT /*FIELDS*/ FROM /*TABLE*/ /*JOIN*/ /*WHERE*/ /*GROUP*/ /*HAVING*/ /*ORDER*/ /*LIMIT*/ /*UNION*/ /*LOCK*/';
	
	private $db = NULL;
	private static $_instance = NULL;
	
	private function __construct(){}
	
	public static function getInstance()
	{
		if ( NULL === self::$_instance ) {
			Application::config()->load('db');
		}
		return self::$_instance;
	}
	
	public function getDbDriver()
	{
		
	}
	
	public function getDbName()
	{
		
	}
	
	public function factory($driver)
	{
		// 载入接口文件
		import_file('libraries.driver.db.interface');
		
		$instance = FALSE;
		switch($driver) {
			case 'mysql':
				import_file('libraries.driver.db.mysql');
				$instance = new Db_Mysql();
			break;
			case 'mysqli':
				import_file('libraries.driver.db.mysqli');
				$instance = new Db_Mysqli();
			break;
		}
		return $instance;
	}
	
	public function dbPrefix($table = '')
	{
		if ('' !== $table) {
			return $this->dbprefix . $table;
		}
		return $this->dbprefix;
	}
	
	public function
}
?>