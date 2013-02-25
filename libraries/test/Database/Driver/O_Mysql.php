<?php
/**
 * PHP-MySQL Claw DB adapter
 *
 * @package claw.ext
 * @subpackage ClawDB
 * @author Tomas Varaneckas <tomas [dot] varaneckas [at] gmail [dot] com>
 * @version $Id: MySQLClawDB.php 122 2006-03-25 15:44:02Z spajus $
 */
class Grace_Database_Driver_Mysql extends Grace_Database_Db implements Grace_Database_Interface
{
	/**
	 * Current result set..
	 *
	 * @var MySQL Result Set
	 */
	protected $_result = NULL;

	/**
	 * Connects to database
	 *
	 * @param Array $cfg array('host', 'user', 'pass', 'db')
	 * @throws DBClawException when connection fails
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function connect($conf)
	{
		if (!function_exists('mysql_connect')) {
			throw new Grace_Database_Exception_Mysql('MySQL php extension not loaded');
		}
		
		if (!@mysql_connect($conf['host'], $conf['user'], $conf['pass'])) {
			throw new Grace_Database_Exception_Mysql('Cannot connect to ' . $conf['host']);
		}
		
		if (!empty($conf['db'])) {
			if (!@mysql_select_db($conf['db'])) {
				throw new Grace_Database_Exception_Mysql('Cannot select db ' . $conf['db']);
			}
		}
		return $this;
	}

	/**
	 * Disconnector
	 * 
	 * @return MySQLClawDB returns self (for fluent interfaces) 
	 */
	public function disconnect()
	{
		@mysql_close();
		return $this;
	}

	/**
	 * Gets an associative array of fields => values
	 * by table, primary key value, and primary key field
	 *
	 * @param string $table Table to get data from
	 * @param string $pk_value Primary Key value
	 * @param string $pk_field Primary Key field (default = 'id')
	 * @return Array
	 */
	public function getByPK($table, $pk_value, $pk_field = 'id')
	{
		return $this->getRow('select * from `' . $table . '` where `'
			. $pk_field . '` = \'' . mysql_escape_string($pk_value) . '\'');
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

	public function delete($table, $pk_value, $pk_field = 'id')
	{
		if ($pk_value) {
			$sql = 'DELETE FROM `' . $table . '` WHERE `' . $pk_field . '` = ' . $this->escape($pk_value);
			return $this->query($sql);
		}
		return FALSE;
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

	/**
	 * Gets array of arrays of rows with data from SQL query.
	 * Mind the portability!
	 *
	 * @param string $sql SQL query
	 * @param Array $params Array of query parameters. Can hold fetchmode
	 *        or variables that need to be bound..
	 * @return Array
	 */
	public function &getArray($sql, $params = array('mode' => MYSQL_ASSOC))
	{
		$result = array();
		$this->query($sql);
		while ($r = $this->fetch($params))
		{
			$result[] = $r;
		}
		$this->free();
		return $result;
	}

	/**
	 * Gets array of row data from SQL query
	 * Mind the portability!
	 *
	 * @param string $sql SQL query
	 * @param Array $params Array of query parameters. Can hold fetchmode
	 *        or variables that need to be bound
	 */
	public function &getRow($sql, $params = array('mode' => MYSQL_ASSOC))
	{
		$this->query($sql);
		$result = $this->fetch($params);
		$this->free();
		return $result;
	}

	/**
	 * Gets single cell (usually string, not array!) from SQL query
	 * Mind the portability!
	 *
	 * @param string $sql SQL query
	 * @param Array $params Array of query parameters. Can hold fetchmode
	 *        or variables that need to be bound
	 */
	public function &getCell($params = array())
	{
		$result = $this->fetch(array('mode' => MYSQL_NUM));
		if (is_array($result))
		{
			$result = array_pop($result);
		}
		return $result;
	}

	/**
	 * Gets result set (whatever the kind..) from SQL query
	 * Should assign the result to ClawDB::$result
	 * Mind the portability!
	 *
	 * @param string $sql SQL query
	 * @param Array $params Array of query parameters. Can hold fetchmode
	 *        or variables that need to be bound
	 */
	public function query($sql, $params = array())
	{
		if (!($this->_result = @mysql_query($sql)))
		{
			throw new Grace_Database_Exception_Mysql('Invalid SQL Query: ' . $sql . ' ('
				. mysql_error() . ')');
		}
		return $this;
	}

	/**
	 * Fetches next result from result set that derrived from ClawDB::query
	 * Result must be at ClawDB::$result
	 *
	 * @param Array $params Array of parameters.. Can hold fetchmode
	 */
	public function fetch($params = array('mode' => MYSQL_ASSOC))
	{
		if ($this->_result == NULL || ! is_resource($this->_result)) {
			throw new Grace_Database_Exception_Mysql('Result Is Not Resourse! ');
		}
		$data = mysql_fetch_array($this->_result, $params['mode']);
		$this->free();
		return $data;
	}

	/**
	 * Releases the ClawDB::$result
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function free()
	{
		mysql_free_result($this->_result);
		$this->_result = NULL;
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
	public function getLastInsertPK()
	{
		return mysql_insert_id();
	}
	
	public function getAffectedRows()
	{
		return mysql_affected_rows();
	}
}
?>