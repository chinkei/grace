<?php
class Driver_Mysql extends Db
{
	public function conn_db($config)
	{
		$conn = FALSE;
		
		if (FALSE === $config['pconnect']) {
			$conn = $this->_connect($config);
		} else {
			$conn = $this->_pconnect($config);
		}
		
		if ( ! empty($config['db']) ) {
			$tshi->db->select($config['db'], $conn);
		}
		return $conn;
	}
	
	private function _connect(&$cfg)
	{
		if ( ! function_exists('mysql_connect') ) {
			
		}
		
		if ( ! (  $conn = mysql_connect($cfg['host'], $cfg['user'], $cfg['pass']) ) ) {
			
		}
		
		return $conn;
	}
	
	private function _pconnect(&$cfg)
	{
		if ( ! function_exists('mysql_pconnect') ) {
			
		}
		
		if ( ! ( $conn = mysql_pconnect($cfg['host'], $cfg['user'], $cfg['pass']) ) ) {
			
		}
		
		return $conn;
	}
	
	public function db_select()
	{
		
	}
	
	public function db_set_charset()
	{
		
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
		return $this->query($sql);
	}
	
	/**
	 * 更新记录
	 * 
	 * @param  string $table    表名
	 * @param  array  $data     数据
	 * @param  string $pk_value 主键值
	 * @param  string $pk_field 主键
	 * @return bool 是否执行成功
	 */
	public function update($table, $data = array(), $pk_value, $pk_field = 'id')
	{
		$uData = array();
		foreach ($data as $field => &$value) {
			$uData[] = '`' . $field . '` = \'' . $this->escape($value) . '\'';
		}
		
		$sql = 'UPDATE `' . $table . '` SET ' . implode(', ' $uData) .
			   'WHERE `' . $pk_field = '` = \'' . $this->escape($pk_value) . '\'';
		
		return $this->query($sql);
	}
	
	public function delete($table, $pk_value, $pk_field = 'id')
	{
		if ($pk_value) {
			$sql = 'DELETE FROM `' . $table . '` WHERE `' . $pk_field . '` = ' . $this->escape($pk_value);
			return $this->query($sql);
		}
		return FALSE;
	}
	
	/**
	 * Fetches next result from result set that derrived from ClawDB::query
	 * Result must be at ClawDB::$result
	 *
	 * @param Array $params Array of parameters.. Can hold fetchmode
	 */
	public function fetch($result, $result_type = MYSQL_ASSOC)
	{
		return mysql_fetch_array($result, $result_type);
	}
	
	/**
	 * Releases the ClawDB::$result
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function free($result)
	{
		return mysql_free_result($result);
	}
	
	public function &getArray($sql, $result_type = MYSQL_ASSOC)
	{
		$retData = array();
		$result  = $this->query($sql);
		
		while ($data = $this->fetch($result, $result_type)){
			$retData[] = $data;
		}
		$this->free($result);
		return $retData;
	}
	
	/**
	 * Escaper
	 *
	 * @param string $value not so clean value
	 * @return string clean value
	 */
	public function escape($value)
	{
		return mysql_real_escape_string($value);
	}
	
	/**
	 * Returns last insert id
	 *
	 * @return mixed
	 */
	public function getInsertId()
	{
		$link = $this->_conn_link['WR'];
		if ($_rw) {
			$link = $this->_conn_link['W+'];
		}
		
		if ( ! is_resource($link) && ! is_object($link) ) {
			return FALSE;
		}
		
		return mysql_insert_id($link);
	}
	
	
	public function get_version()
	{
		
	}
	
	
	/**
	 * 关闭数据库连接
	 * 
	 * @param  string $mode 数据模式
	 * @return bool 
	 */
	public function db_close($mode = 'WR')
	{
		if ($mode == 'R+' || $mode == 'W+') {
			if ( isset($this->_conn_link[$mode]) ) {
				if (is_resource($this->_conn_link[$mode]) || is_object($this->_conn_link[$mode])) {
					mysql_close($this->_conn_link[$mode]);
				}
				unset($this->_conn_link[$mode]);
			}
			return TRUE;
		}
		
		foreach ($this->_conn_link as $link) {
			if ( is_resource($link) || is_object($link) ) {
				mysql_close($link);
			}
		}
		$this->_conn_link = array();
		return TRUE;
	}
}
?>