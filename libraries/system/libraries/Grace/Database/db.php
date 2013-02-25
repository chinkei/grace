<?php
$driver = 'mysql';
$weight = FALSE;

$config = {
	'hostname' => '10.1.16.137',
	'username' => 'root',
	'password' => 'esocc',
	'database' => 'esocc',
	'dbport' => '3306',
}

$rconfig = {
	array(
		'hostname' => '10.1.16.137',
		'username' => 'root',
		'password' => 'esocc',
		'database' => 'esocc',
		'dbport' => '3306',
		'weight' => '50'
	),
	array(
		'hostname' => '10.1.16.137',
		'username' => 'root',
		'password' => 'esocc',
		'database' => 'esocc',
		'dbport' => '3306',
		'weight' => '100'
	),
	array(
		'hostname' => '10.1.16.137',
		'username' => 'root',
		'password' => 'esocc',
		'database' => 'esocc',
		'dbport' => '3306',
		'weight' => '200'
	)
}

abstract class Db
{
	protected $username;
	protected $password;
	protected $hostname;
	protected $database;
	protected $dbdriver		= 'mysql';
	protected $dbport = '3306';
	
	protected $_conn_link = array();
	protected $_is_
	
	private static $_instance  = FALSE;
	
	abstract public function conn_db();
	abstract public function reconnect();
	abstract public function db_select();
	abstract public function db_set_charset();
	abstract public function get_version();
	
	/**
	 * 支持读写分离
	 * @param string $mode WR|R|W
	 */
	public static function getInstance()
	{
		global $driver;
		
		if (FALSE !== self::$_instance) {
			import_file('libraries.database.connectors.driver.conn_'.$driver);
			self::$_instance = new 'Conn_' . ucfirst($driver);
		}
		return self::$_instance;
	}
	
	/**
	 * 支持读写分离
	 * @param string $mode WR|R|W
	 */
	public function getConnLink($mode = 'WR')
	{
		global $config, $rconfig, $weight;
		
		if ( ! isset($this->_conn_link[$mode]) || ( ! is_resource($this->_conn_link[$mode]) && ! is_object($this->_conn_link[$mode]) ) ) 
		{
			$link = FALSE;
			switch ($mode) {
				case 'R+':
					if ($weight)
					$link = $this->getSlaveLink($rconfig, $weight);
				break;
				
				case 'W+':
				case 'WR':
					$link = $this->conn_db($config);
				break;
			}
			
			if (FALSE === $link) {
				exit('数据库连接错误！');
			}
			$this->_conn_link[$mode] = $link;
		}
		return $this->_conn_link[$mode];
	}
	
	/**
	 * 连接数据库
	 */
	protected function conn_db($config)
	{
		return $conn;
	}
	
	/**
	 * 获取从库连接句柄
	 */
	protected function _getSlaveLink($rconfig, $weight)
	{
		global $rconfig, $weight;
		$slave = $this->_getSlaveConfig($rconfig, $weight);
		if ( FALSE === ($conn = $this->conn_db($slave['config'])) ) {
			if (empty($rconfig)) {
				// 从库都连接失败
				return FALSE;
			}
			
			unset($rconfig[$slave['index']]);
			$rconfig = array_merge($rconfig);
			return $this->_getSlaveLink($rconfig, $weight);
		}
		return $conn;
	}
	
	/**
	 * 获取从库配置
	 */
	protected function _getSlaveConfig($rconfig, $isWeight = FALSE)
	{
		$result = array();
		$count = count($rconfig);
		
		// 未启用权重功能
		if (FALSE == $isWeight) {
			$index = mt_rand(0, $count - 1);
			$result['index']  = $index;
			$result['config'] = $rconfig[$index];
		} else {
			$weight = -1;
			foreach($rconfig as &$val) {
				$val['start'] = $weight + 1;
				$weight += $val['weight'];
				$val['end'] = $weight;
			}
			
			$rand = mt_rand(0, $weight);
			
			for ($i = 0; $i < $count; $i++) {
				if ($rand >= $rconfig[$i]['start'] && $rand <= $rconfig[$i]['end']) {
					$result['index']  = $i;
					$result['config'] = $rconfig[$i];
					break;
				}
			}
		}
		return $result;
		
	}
}
?>