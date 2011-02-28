<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * Postgresql数据库操作类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Database
 */
class Grace_Database_Driver_Postgresql extends Grace_Database_Driver
{
	/**
	 * Current result set..
	 *
	 * @var PostgreSQL Result Set
	 */
	protected $result = null;

	/**
	 * Connection
	 */
	protected $conn = null;

	/**
	 * Connects to database
	 *
	 * @param Array $cfg array('host', 'user', 'pass', 'db')
	 * @throws DBClawException when connection fails
	 * @return PostgreSQLClawDB returns self (for fluent interfaces)
	 */
	public function connect($conf)
	{
		if (!function_exists('pg_connect'))
		{
			throw new DBClawException('PostgreSQL php extension not loaded');
		}
		$connectstring = 'host=' . $conf['host'] . ' dbname=' . $conf['db']
			. ' user=' . $conf['user'] . ' password=' . $conf['pass'];
		$this->conn = @pg_connect($connectstring);
		if (!$this->conn)
		{
			throw new DBClawException('Cannot connect to ' . $conf['host']);
		}
		return $this;
	}

	/**
	 * Disconnector
	 * 
	 * @return OracleClawDB returns self (for fluent interfaces)
	 */
	public function disconnect()
	{
		@pg_close($this->conn);
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
		// SELECT * FROM "$table" WHERE "$pk_field" = escape($pk_value)
		return $this->getRow('SELECT * FROM "' . $table . '" WHERE "'
			. $pk_field . '" = ' . $this->escape($pk_value) );
	}

	/**
	 * Define table and provide array of data to be inserted, this function
	 * does the operation
	 *
	 * @param string $table Table we are inserting data into
	 * @param Array $data Associative array of data to be inserted
	 * @return OracleClawDB returns self (for fluent interfaces)
	 */
	public function insert($table, $data = array())
	{
		// INSERT INTO "$table" ("col1", "col2") VALUES ( escape(val1), escape(val2) )
		$fields = implode('", "', array_keys($data));
		foreach ($data as $key => &$value)
		{
			$value = $this->escape($value);
		}
		$values = implode(', ', array_values($data));
		$this->query('INSERT INTO "' . $table . '" ("' . $fields
			. '") VALUES (\'' . $values . '\')');
		return $this;
	}

	/**
	 * Deletes entry from table by primary key
	 *
	 * @param string $table
	 * @param mixed $pk_value
	 * @param string $pk_field
	 * @return OracleClawDB returns self (for fluent interfaces)
	 */
	public function delete($table, $pk_value, $pk_field = 'id')
	{
		// DELETE FROM "$table" WHERE "$pk_field" = escape($pk_value)
		if ($pk_value)
		{
			$this->query('DELETE FROM "' . $table . '" WHERE "'
				. $pk_field . '" = ' . $this->escape($pk_value));
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
	 * @return OracleClawDB returns self (for fluent interfaces)
	 */
	public function update($table, $data = array(), $pk_value, $pk_field = 'id')
	{
		// UPDATE "$table" SET "col1" = escape(val1), ... WHERE "$pk_field"
		//= escape($pk_value)
		$sql = array();
		foreach ($data as $field => &$value)
		{
			$sql[] = '"' . $field . '" = ' . $this->escape($value);
		}
		$this->query('UPDATE "' . $table . '" SET ' . implode(', ', $sql)
			. ' WHERE "' . $pk_field . '" = ' . $this->escape($pk_value) );
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
	public function &getArray($sql, $params = array())
	{
		$result = array();
		$this->query($sql, $params);
		while ($r = $this->fetch())
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
	public function &getRow($sql, $params = array())
	{
		$this->query($sql, $params);
		$result = $this->fetch();
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
		$result = $this->fetch(array('mode' => PGSQL_ASSOC));
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
		if (count($params))
		{
			$this->result = @pg_query_params($this->conn, $sql, $params);
		}
		else
		{
			$this->result = @pg_query($this->conn, $sql);
		}
		if (!$this->result)
		{
			throw new DBClawException('Invalid SQL Query: ' . $sql . ' ('
				. pg_last_error($this->conn) . ')');
		}
		return pg_affected_rows( $this->result );
	}

	/**
	 * Fetches next result from result set that derrived from ClawDB::query
	 * Result must be at ClawDB::$result
	 *
	 * @param Array $params Array of parameters.. Can hold fetchmode
	 */
	public function fetch($params = array('mode' => PGSQL_ASSOC))
	{
		return pg_fetch_array($this->result, NULL, $params['mode']);
	}

	/**
	 * Fetches next result as object from result set that derrived from ClawDB::query
	 * Result must be at ClawDB::$result
	 *
	 * @return object with one attribute for each field name in the result
	 */
	public function fetchObject()
	{
		return pg_fetch_object($this->result);
	}

	/**
	 * Releases the ClawDB::$result
	 * 
	 * @return OracleClawDB returns self (for fluent interfaces)
	 */
	public function free()
	{
		pg_free_result($this->result);
		return $this;
	}

	/**
	 * Escaper
	 *
	 * @param $value not so clean value (string, number, bool, NULL)
	 * @return string clean value
	 */
	public function escape($value)
	{
		if (is_string($value))
		{
			return "'" . pg_escape_string($value) . "'";
		}
		if (is_numeric($value))
		{
			return $value;
		}
		if (is_bool($value))
		{
			return $value ? 'TRUE' : 'FALSE';
		}
		if (!isset($value))
		{
			return 'NULL';
		}
		throw new DBClawException('Invalid value type');
	}

	/**
	 * Returns last insert id
	 *
	 * @return mixed
	 * @todo implementation
	 */
	public function getLastInsertPK()
	{
		return null; //todo
	}
}
?>