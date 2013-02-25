<?php
class Grace_Database_Driver_Mysql extends Grace_Database_Db
{
	
	public function __construct($settings)
	{
		$this->_db_settings = $settings;
	}
	
	public function connect($dbConf)
	{
		if ( !isset($dbConf['hostname']) || !isset($dbConf['username']) || !isset($dbConf['password']) || !isset($dbConf['database']) ) {
			trigger_error('config is error');
		}
		
		// isset($dbConf['dbport'])
		
		//连接数据库主机   
        $link = mysql_connect($dbConf['hostname'], $dbConf['username'], $dbConf['password'], true);   
        if ( !$link ) {   
            $this->errorLog('Mysql connect '. $dbConf['hostname'] .' failed');   
            return FALSE;   
        }
		
		//选定数据库   
        if ( !mysql_select_db($dbConf['database'], $link) ) {   
            $this->errorLog('select db ' .$dbConf['database']. ' failed', $link);
			mysql_close($link);
            return false;   
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
	 * @param  string $dbType   数据库连接类型
	 * @return bool
	 */
	public function disconnect($isCloseAll = FALSE, $dbType = 'WR')
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
	
	public function execute($sql)
	{
		if (!$this->_query($sql, TRUE)){   
            return FALSE;   
        }
		return TRUE;
	}
	
	public function query($sql)
	{
		if (!$this->_query($sql, FALSE)){   
            return FALSE;   
        }
		return TRUE;
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
		
		$sql = 'INSERT INTO `' . $table . '` (`' . $fields . '`) VALUES (\'' . $values . '\')';
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
		
		$sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $uData) . ' ' .
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
		
		$sql = 'DELETE FROM `' . $table . '` WHERE ' . $where;
		return $this->_query($sql);
	}
	
	public function resultArray()
	{
		
	}
	
	public function rowArray()
	{
		
	}
	
	public function colArray()
	{
		
	}
	
	public function rowColOne()
	{
		
	}
	
	public function getInsertId()
	{
		
	}
	
	public function getLastSql()
	{
		
	}
	
	public function free()
	{
		
	}
	
	public function getVersion()
	{
		
	}
	
	public function escape($str)
	{
		return mysql_real_escape_string($this->currConn, $str);
	}
	
	protected function _query($sql)
	{
		// TODO 要验证是否主库或从库
		return mysql_query($sql);
	}
}
?>