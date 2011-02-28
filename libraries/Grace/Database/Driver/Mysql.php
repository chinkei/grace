<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

uses('Grace_Database_Exception_Mysql');

/**
 * mysql数据库操作类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Database
 */
class Grace_Database_Driver_Mysql extends Grace_Database_Db
{
	/**
	 * 构造方法
	 * 
	 * @param  array $settings 配置项数组
	 * @return void
	 */
	public function __construct($settings)
	{
		$this->_db_settings = $settings;
	}
	
	/**
	 * 连接数据库
	 * 
	 * @param  array $dbConf 数据库连接配置
	 * @return resource|bool
	 */
	public function connect($dbConf)
	{
		if ( !isset($dbConf['hostname']) || !isset($dbConf['username']) || !isset($dbConf['password']) || !isset($dbConf['database']) ) {
			throw new Grace_Database_Exception_Mysql('lack of configuration parameters');
		}
		
		if (isset($dbConf['dbport'])) {
			$dbConf['hostname'] .= ':'.$dbConf['dbport'];
		}
		
		//连接数据库主机   
        $link = mysql_connect($dbConf['hostname'], $dbConf['username'], $dbConf['password'], TRUE);   
        if ( !$link ) {
			throw new Grace_Database_Exception_Mysql('Mysql Connect '. $dbConf['hostname'] .' failed');
        }
		
		//选定数据库   
        if ( !mysql_select_db($dbConf['database'], $link) ) {
			$this->disconnect(FALSE, $link);
			throw new Grace_Database_Exception_Mysql('Select DB  '. $dbConf['hostname'] .'@ ' . $dbConf['database'] . '  failed');
        }
		
		// 设定编码类型
		if ( ( $charset = $this->getCharset() ) !== FALSE) {
			mysql_query("SET NAMES '$charset'", $link);
		}
		return $link;
	}
	
	/**
	 * 关闭数据库连接
	 * 
	 * @param  bool   $isCloseAll 是否关闭所有
	 * @param  string $dbType     数据库连接类型
	 * @return bool
	 */
	public function disconnect($isCloseAll = FALSE, $dbType = Grace_Database_Db::PATTERN_MASTER)
	{
		if ($isCloseAll === TRUE) {
			foreach ($this->__conn_link as $link) {
				if ($link && is_resource($link)){   
                    mysql_close($link);   
                }
			}
			$this->_conn_link = array();
			return TRUE;
		}
		
		if (isset($this->_conn_link[$dbType]) && is_resource($dbType)) {
			mysql_close($this->_conn_link[$dbType]); 
			unset($this->_conn_link[$dbType]);
		}
		return TRUE;
	}
	
	/**
 	 * 执行主库sql语句
	 * 
	 * @param  string $sql sql语句
	 * @return bool 是否成功
	 */
	public function execute($sql)
	{
		if ( !$this->_query($sql) ) {   
            return FALSE;   
        }
		return $this;
	}
	
	/**
 	 * 执行从库sql语句
	 * 
	 * @param  string $sql sql语句
	 * @return bool 是否执行成功
	 */
	public function query($sql)
	{
		if ( !$this->_query($sql, FALSE) ) {   
            return FALSE;   
        }
		return $this;
	}
	
	/**
	 * 插入记录
	 * 
	 * @param  string $table 表名
	 * @param  array  $data  数据
	 * @return bool 是否执行成功
	 */
	public function insert($table, $data = array())
	{
		if ( !is_array($data) || empty($data) ) {
			return FALSE;
		}
		
		foreach ($data as &$value) {
			$value = $this->escape($value);
		}
		$fields = implode('`, `', array_keys($data));
		$values = implode('\', \'', array_values($data));
		
		$sql = 'INSERT INTO `' . $this->dbPrefix($table) . '` (`' . $fields . '`) VALUES (\'' . $values . '\')';
		return $this->_query($sql);
	}
	
	/**
	 * 更新记录
	 * 
	 * @param  string $table 表名
	 * @param  array  $data  数据
	 * @param  mixed  $where 过滤条件(string|array)
	 * @return bool 是否执行成功
	 */
	public function update($table, $data = array(), $where = NULL)
	{
		if ( !is_array($data) || empty($data) || $where == NULL ) {
			return FALSE;
		}
		
		$uData = array();
		foreach ($data as $field => &$value) {
			$uData[] = '`' . $field . '` = \'' . $this->escape($value) . '\'';
		}
		
		if ( is_array($where) ) {
			$filter = $where;
			$wData  = array();
			foreach ($filter as $field => &$value) {
				$wData[] = '`' . $field . '` = \'' . $this->escape($value) . '\'';
			}
			$where = implode(' AND ', $wData);
		}
		
		$sql = 'UPDATE `' . $this->dbPrefix($table) . '` SET ' . implode(', ', $uData) . ' ' .
			   'WHERE ' . $where;
		
		return $this->_query($sql);
	}
	
	/**
	 * 删除记录
	 * 
	 * @param  string $table 表名
	 * @param  array  $data  数据
	 * @param  mixed  $where 过滤条件(string|array)
	 * @return bool 是否执行成功
	 */
	public function delete($table, $where = NULL)
	{
		if ( $where == NULL ) {
			return FALSE;
		}
		
		if ( is_array($where) ) {
			$filter = $where;
			$wData  = array();
			foreach ($filter as $field => &$value) {
				$wData[] = '`' . $field . '` = \'' . $this->escape($value) . '\'';
			}
			$where = implode(' AND ', $wData);
		}
		
		$sql = 'DELETE FROM `' . $this->dbPrefix($table) . '` WHERE ' . $where;
		return $this->_query($sql);
	}
	
	/**
	 * 获取记录集合
	 * 
	 * @param  string $type 数据模式
	 * @return array
	 */
	public function resultArray($type = MYSQL_ASSOC)
	{
		$data = array();
		 
        while ($row = mysql_fetch_array($this->_result, $type)) {   
            $data[] = $row;   
        }
		// 释放结果集内存
		$this->free();
        return $data;
	}
	
	/**
	 * 获取单行记录
	 * 
	 * @param  string $type 数据模式
	 * @return array
	 */
	public function rowArray($type = MYSQL_ASSOC)
	{
		$data = mysql_fetch_array($this->_result, $type);
		// 释放结果集内存
		$this->free();
		
		return $data;
	}
	
	/**
	 * 获取某个字段列数据(空时默认为第一列数据)
	 * 
	 * @param  string $field 字段名
	 * @return array
	 */
	public function colArray($field = '')
	{
		$data  = array();
		$field = trim($field);
		
        while ($row = mysql_fetch_array($this->_result, MYSQL_ASSOC)) {
			if ($field == '') {
				$data[] = current($row);
			} else {
				$data[] = $row[$field];
			}
		}
		// 释放结果集内存
		$this->free();
        return $data; 
	}
	
	/**  
     * 获取一个数据(当条数组)  
     *  
     * @param string $sql 需要执行查询的SQL  
     * @return 成功返回获取的一个数据,失败返回false, 数据空返回NULL  
     */  
    public function getRowColOne($field = '')
	{     
        $row = mysql_fetch_array($this->_result, $this->fetchMode);
		$this->free();
		
        if ( !is_array($row) || empty($row) ){   
            return NULL;   
        }
		
		$data = NULL;
		
        if (trim($field) != ''){   
            $data = $row[$field];   
        } else {   
            $data = current($row);   
        }   
        return $data;   
    }
	
	/**
	 * 获取上次INSERT 操作产生的 ID 
	 * 
	 * @return int
	 */
	public function getInsertId()
	{
		$dbConn = $this->getConnLink(Grace_Database_Db::PATTERN_MASTER);
		
		if ( ($lastId = mysql_insert_id($dbConn)) > 0 ){   
            return $lastId;   
        }
		return $this->execute("SELECT LAST_INSERT_ID()")->getOne();
	}
	
	/**
	 * 获取上次执行的sql语句
	 * 
	 * @return string
	 */
	public function getLastSql()
	{
		return $this->_lastSql;
	}
	
	/**
	 * 取得前一次 MySQL 操作所影响的记录行数
	 * 
	 * @param  resource $dbConn 数据库连接句柄
	 * @return int 所影响的行数
	 */
	public function affectedRows($dbConn = NULL)
	{
		// 参数句柄为空时, 默认使用主库句柄
		if ( $dbConn == NULL ) {
			$dbConn = $this->getConnLink(Grace_Database_Db::PATTERN_MASTER);
		}
		
		if ( is_resource($dbConn) ) {
			return mysql_affected_rows($dbConn);
		}
		return 0;
	}
	
	/**
	 * 释放结果内存
	 * 
	 * @param  resource $result 结果集
	 * @return void
	 */
	public function free($result = NULL)
	{
		if ( $result != NULL && is_resource($result) ) {
			mysql_free_result($result);
		} else {
			if ( $this->_result != NULL && is_resource($this->_result) ) {
				mysql_free_result($this->_result);
				$this->_result = NULL;
			}
		}
	}
	
	/**
	 * 获取数据库版本类型
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return '114';
	}
	
	/**
	 * 转义成安全字符串
	 *
	 * @param  string $str 要转义的字符串
	 * @return string 转义后的字符串
	 */
	public function escape($str)
	{
		return mysql_real_escape_string($str);
	}
	
	/**
	 * 执行SQL语句
	 * 
	 * @param  string $sql      SQL语句
	 * @param  bool   $isMaster 是否是主从模式
	 * @return bool
	 */
	protected function _query($sql, $isMaster = TRUE)
	{
		$sql = trim($sql);
		if ( $sql == '' ) {
			throw new Grace_Database_Exception_Mysql("Sql query is empty.");
            return FALSE;
		}
   
   		$optType = strtolower(substr($sql, 0, 6));
		
        if ( $isMaster || $optType != "select" ) {
			$dbConn = $this->getConnLink(Grace_Database_Db::PATTERN_MASTER);
        } else {   
            $dbConn = $this->getConnLink(Grace_Database_Db::PATTERN_SLAVE);
        }
		
  		$this->_currConn = $dbConn;
		$this->_lastSql  = $sql;
		// 释放上次结果缓存
		$this->free();
		
		$this->_result = mysql_query($sql, $dbConn);
		
        if ( $this->_result === FALSE ) {
			$errMsg = 'MySQL Errno:'. mysql_errno($dbConn) .', error:'. mysql_error($dbConn);
			throw new Grace_Database_Exception_Mysql($errMsg);
            return FALSE;
        }
        return TRUE;
	}
}
?>