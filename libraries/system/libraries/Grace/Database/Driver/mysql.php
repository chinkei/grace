<?php

/**
 * PHP-MySQL Claw DB adapter
 *
 * @package claw.ext
 * @subpackage ClawDB
 * @author Tomas Varaneckas <tomas [dot] varaneckas [at] gmail [dot] com>
 * @version $Id: MySQLClawDB.php 122 2006-03-25 15:44:02Z spajus $
 */
class MySQLClawDB implements ClawDB
{
	/**
	 * Current result set..
	 *
	 * @var MySQL Result Set
	 */
	protected $result = null;

	/**
	 * Connects to database
	 *
	 * @param Array $cfg array('host', 'user', 'pass', 'db')
	 * @throws DBClawException when connection fails
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function connect($cfg)
	{
		if (!function_exists('mysql_connect'))
		{
			throw new DBClawException('MySQL php extension not loaded');
		}
		if (!@mysql_connect($cfg['host'], $cfg['user'], $cfg['pass']))
		{
			throw new DBClawException('Cannot connect to ' . $cfg['host']);
		}
		if (!empty($cfg['db']))
		{
			if (!@mysql_select_db($cfg['db']))
			{
				throw new DBClawException('Cannot select db ' . $cfg['db']);
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
	 * Define table and provide array of data to be inserted, this function
	 * does the operation
	 *
	 * @param string $table Table we are inserting data into
	 * @param Array $data Associative array of data to be inserted
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function insert($table, $data = array())
	{
		$fields = implode('`, `', array_keys($data));
		foreach ($data as $key => &$value)
		{
			$value = mysql_escape_string($value);
		}
		$values = implode('\', \'', array_values($data));
		$this->query('insert into `' . $table . '` (`' . $fields
			. '`) values (\'' . $values . '\')');
		return $this;
	}

	/**
	 * Deletes entry from table by primary key
	 *
	 * @param string $table
	 * @param mixed $pk_value
	 * @param string $pk_field
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function delete($table, $pk_value, $pk_field = 'id')
	{
		if ($pk_value)
		{
			$this->query('delete from `' . $table . '` where `'
				. $pk_field . '` = ' . $this->escape($pk_value));
		}
		return $this;
	}

	/**
	 * Update method, which is meant to modify ONE record BY primary key
	 *
	 * @param string $table Table we are updating
	 * @param Array $data Associative array of data to be updated
	 * @param mixed $pk_value Value of primary key of record we're updating
	 * @param string $pk_field Primary Key field (default - 'id')
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function update($table, $data = array(), $pk_value, $pk_field = 'id')
	{
		$sql = array();
		foreach ($data as $field => &$value)
		{
			$sql[] = '`' . $field . '` = \''
			             . mysql_escape_string($value) . '\'';
		}
		$this->query('update `' . $table . '` set ' . implode(', ', $sql)
			. ' where `' . $pk_field . '` = \''
			. mysql_escape_string($pk_value) . '\'');
		return $this;
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
	public function &getCell($sql, $params = array())
	{
		$this->query($sql);
		$result = $this->fetch(array('mode' => MYSQL_NUM));
		if (is_array($result))
		{
			$result = array_pop($result);
		}
		$this->free();
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
		if (!($this->result = @mysql_query($sql)))
		{
			throw new DBClawException('Invalid SQL Query: ' . $sql . ' ('
				. mysql_error() . ')');
		}
		return mysql_affected_rows();
	}

	/**
	 * Fetches next result from result set that derrived from ClawDB::query
	 * Result must be at ClawDB::$result
	 *
	 * @param Array $params Array of parameters.. Can hold fetchmode
	 */
	public function fetch($params = array('mode' => MYSQL_ASSOC))
	{
		return mysql_fetch_array($this->result, $params['mode']);
	}

	/**
	 * Releases the ClawDB::$result
	 * @return MySQLClawDB returns self (for fluent interfaces)
	 */
	public function free()
	{
		mysql_free_result($this->result);
		return $this;
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
}
?>