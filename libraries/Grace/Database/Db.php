<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

uses('Grace_Configure_Driver');

/**
 * 数据库库抽象类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Database
 */
abstract class Grace_Database_Db
{
	/**
	 * 主库模式
	 * 
	 * @var const
	 */
	const PATTERN_MASTER  = 'master';
	
	/**
	 * 从库模式
	 * 
	 * @var const
	 */
	const PATTERN_SLAVE   = 'slave';
	
	/**
	 * 数据库连接句柄数组
	 * 
	 * @var array [resource]
	 */
	protected $_conn_link    = array();
	
	/**
	 * 当前的数据库连接句柄
	 *
	 * @param resource 连接句柄
	 */
	protected $_currConn = NULL;
	
	/**
	 * 当前的数据库连接句柄
	 *
	 * @param resource 连接句柄
	 */
	protected $_result   = NULL;
	
	/**
	 * 最后次执行的sql语句
	 *
	 * @param string 
	 */
	protected $_lastSql  = '';
	
	/**
	 * 数据库配置数组
	 * 
	 * @var array
	 */
	protected $_db_settings  = array();
	
	/**
	 * 数据库驱动对象数组
	 * 
	 * @var array
	 */
	private static $_instance  = array();
	
	/**
	 * 获取数据库驱动对象
	 * 
	 * @param string $param   配置项索引
	 * @param string $default 默认配置项索引
	 * @reutn object
	 */
	public static function loadDriver($param = '', $default = 'default')
	{
		// 获取配置信息
		$db = Grace_Configure_Driver::build(APP_PATH.'/config', 'php')->read('db');
		
		if ( count($db) == 0) {
			show_error('No database connection settings were found in the database config file.');
		}
		
		$active_group = ( $param == '' ? $default : $param);
		
		if ( !isset(self::$_instance[$active_group]) ) {
			if ( !isset($db[$active_group]) ) {
				// TODO
				trigger_error('不存在这个数据模型');
			}
			
			$driver = 'Grace_Database_Driver_' . $db[$active_group]['driver'];
			uses($driver);
			
			// 实例化对象
			self::$_instance[$active_group] = new $driver($db[$active_group]);
		}
		return self::$_instance[$active_group];
	}
	
	/**
	 * 获取数据库连接句柄
	 * 
	 * @param  string  $mode WR|R+|W+ 读写模式
	 * @return resource|object
	 */
	public function getConnLink($mode = self::PATTERN_MASTER)
	{
		// 未开启主从模式或是写模式都默认是主库连接
		if ($this->isMasterSlave() === FALSE || $mode == self::PATTERN_SLAVE ) {
			$mode = self::PATTERN_MASTER;
		}
		
		if ( ! isset($this->_conn_link[$mode]) || ( ! is_resource($this->_conn_link[$mode]) && ! is_object($this->_conn_link[$mode]) ) ) {
			$link = FALSE;
			
			if ($mode == self::PATTERN_SLAVE) {
				$link = $this->_getSlaveLink($this->_db_settings[self::PATTERN_SLAVE]);
				
				// 从库都连接失败, 那继续连接主库
				if ( $link === FALSE ) {
					$mode = self::PATTERN_MASTER;
				}
			}
			
			if ($mode == self::PATTERN_MASTER) {
				$link = $this->connect($this->_db_settings[self::PATTERN_MASTER]);
			}
			
			if (FALSE === $link) {
				exit('数据库连接错误！');
			}
			
			$this->_conn_link[$mode] = $link;
		}
		return $this->_conn_link[$mode];
	}
	
	/**
	 * 获取从库连接句柄
	 * 
	 * @param  array $settings 配置项数组
	 * @return resource|object
	 */
	protected function _getSlaveLink($settings)
	{
		if ( empty($settings) ) {
			// 从库都连接失败
			return FALSE;
		}
			
		$slave = $this->_getSlaveConfig($settings);
		if ( FALSE === ($conn = $this->connect($slave['config'])) ) {
			unset($settings[$slave['index']]);
			return $this->_getSlaveLink($settings);
		}
		return $conn;
	}
	
	/**
	 * 获取从库配置
	 * 
	 * @param  array $settings 配置项数组
	 * @return array
	 */
	protected function _getSlaveConfig($settings)
	{
		$result = array();
		$count = count($settings);
		
		// 未启用权重功能
		if ($this->isUseWeight() === FALSE) {
			$index = mt_rand(0, $count - 1);
			$result['index']  = $index;
			$result['config'] = $settings[$index];
		} else {
			$weight = -1;
			foreach($settings as &$val) {
				$val['start'] = $weight + 1;
				$weight += $val['weight'];
				$val['end'] = $weight;
			}
			
			$rand = mt_rand(0, $weight);
			
			for ($i = 0; $i < $count; $i++) {
				if ($rand >= $settings[$i]['start'] && $rand <= $settings[$i]['end']) {
					$result['index']  = $i;
					$result['config'] = $settings[$i];
					break;
				}
			}
		}
		return $result;
		
	}
	
	/**
	 * 是否是主从模式
	 * 
	 * return bool
	 */
	public function isMasterSlave()
	{
		return isset($this->_db_settings['master_slave']) ? $this->_db_settings['master_slave'] : FALSE;
	}
	
	/**
	 * 从库是否使用权重
	 * 
	 * return bool
	 */
	public function isUseWeight()
	{
		return isset($this->_db_settings['use_weight']) ? $this->_db_settings['use_weight'] : FALSE;
	}
	
	/**
	 * 获取数据编码类型
	 * 
	 * retrun mixed string|false
	 */
	public function getCharset()
	{
		return isset($this->_db_settings['char_set']) ? $this->_db_settings['char_set'] : FALSE;
	}
	
	/**
	 * 获取完整的表名
	 * 
	 * @param  string $table 表名
	 * @return string 完整的数据表名
	 */
	public function dbPrefix($table = '')
	{
		return isset($this->_db_settings['db_prefix']) ? $this->_db_settings['db_prefix'] . $table : $table;
	}
	
	// ---------------------------------------------- 子类需要实现的方法 START -------------------------------------------- //
	
	/**
	 * 连接数据库
	 * 
	 * @param array $dbConf 连接的配置项数组
	 */
	abstract public function connect($dbConf);
	
	/**
	 * 关闭数据库连接
	 * 
	 * @param bool   $isCloseAll 是否关闭所有
	 * @param string $dbType     数据库连接类型
	 */
	abstract public function disconnect($isCloseAll = FALSE, $dbType = Grace_Database_Db::PATTERN_MASTER);
	
	/**
	 * 执行主库SQL语句(主要是CURD)
	 * 
	 * @param string $sql
	 */
	abstract public function execute($sql);
	
	/**
	 * 执行从库SQL语句(主要是SELECT)
	 * 
	 * @param string $sql
	 */
	abstract public function query($sql);
	
	/**
	 * 插入记录
	 * 
	 * @param  string $table 表名
	 * @param  array  $data  数据
	 */
	abstract public function insert($table, $data = array());
	
	/**
	 * 更新记录
	 * 
	 * @param  string $table 表名
	 * @param  array  $data  数据
	 * @param  mixed  $where 过滤条件(string|array)
	 */
	abstract public function update($table, $data = array(), $where = NULL);
	
	/**
	 * 删除记录
	 * 
	 * @param  string $table 表名
	 * @param  mixed  $where 过滤条件(string|array)
	 */
	abstract public function delete($table, $where = NULL);
	
	/**
	 * 获取记录集合
	 * 
	 * @param  string $type 数据模式
	 * @return array
	 */
	abstract public function resultArray($type = MYSQL_ASSOC);
	
	/**
	 * 获取单行记录
	 * 
	 * @param  string $type 数据模式
	 * @return array
	 */
	abstract public function rowArray($type = MYSQL_ASSOC);
	
	/**
	 * 获取某字段列数据(空时默认第一列)
	 * 
	 * @param  string $field 字段名
	 */
	abstract public function colArray($field = '');
	
	/**
	 * 获取单行某字段数据(空时默认第一个数据)
	 * 
	 * @param  string $field 字段名
	 */
	abstract public function getRowColOne($field = '');
	
	/**
	 * 获取上次INSERT操作产生的ID 
	 * 
	 * @return int
	 */
	abstract public function getInsertId();
	
	/**
	 * 获取上次的执行的SQL语句 
	 * 
	 * @return string
	 */
	abstract public function getLastSql();
	
	/**
	 * 取得前一次 MySQL 操作所影响的记录行数
	 * 
	 * @return int
	 */
	abstract public function affectedRows($dbConn = NULL);
	
	/**
	 * 释放结果集内存
	 * 
	 * @return bool
	 */
	abstract public function free();
	
	/**
	 * 转义成安全字符串
	 *
	 * @param  string $str 要转义的字符串
	 * @return string 转义后的字符串
	 */
	abstract public function escape($str);
	
	/**
	 * 获取数据库版本号
	 * 
	 * @return string
	 */
	abstract public function getVersion();
	
	// ---------------------------------------------- 子类需要实现的方法  END  -------------------------------------------- //
}
?>