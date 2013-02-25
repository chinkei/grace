<?php
/**
 * PHP-SQLite Claw DB adapter
 *
 * @package claw.ext
 * @author Tomas Varaneckas <tomas [dot] varaneckas [at] gmail [dot] com>
 * @version $Id: SQLiteClawDB.php 120 2006-03-19 19:26:44Z spajus $
 * @todo testing
 */
class Grace_Database_Driver_Sqlite extends Grace_Database_Driver
{
	/**
	 * Current result set..
	 *
	 * @var SQLite Result Set
	 */
	protected $result = null;

	/**
	 * Database resource
	 *
	 * @var SQLite db resource
	 */
	protected $db = null;
	/**
	 * Connects to database
	 *
	 * @param Array $cfg array('host', 'user', 'pass', 'db')
	 * @throws DBClawException when connection fails
	 * @return SQLiteClawDB return self (for fluent interfaces)
	 */
	public function connect($conf)
	{
		$error = null;
		$this->db = sqlite_open($conf['db'], $conf['mode'], $error);
		if ($error)
		{
			throw new DBClawException('Cannot connect to ' . $conf['db']
				. '(' . $error . ')');
		}
		return $this;
	}

	/**
	 * Disconnector
	 * 
	 * @return SQLiteClawDB return self (for fluent interfaces)
	 */
	public function disconnect()
	{
		@sqlite_close($this->db);
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
		return $this->getRow('select * from ' . $table . ' where '
			. $pk_field . ' = \'' . sqlite_escape_string($pk_value) . '\'');
	}

	/**
	 * Define table and provide array of data to be inserted, this function
	 * does the operation
	 *
	 * @param string $table Table we are inserting data into
	 * @param Array $data Associative array of data to be inserted
	 * @return SQLiteClawDB return self (for fluent interfaces)
	 */
	public function insert($table, $data = array())
	{
		$fields = implode(', ', array_keys($data));
		foreach ($data as $key => &$value)
		{
			$value = sqlite_escape_string($value);
		}
		$values = implode('\', \'', array_values($data));
		$this->query('insert into ' . $table . ' (' . $fields
			. ') values (\'' . $values . '\')');
		return $this;
	}

	/**
	 * Deletes entry by primary key
	 *
	 * @param string $table
	 * @param mixed $pk_value
	 * @param string $pk_field
	 * @return SQLiteClawDB return self (for fluent interfaces)
	 */
	public function delete($table, $pk_value, $pk_field = 'id')
	{
		if ($pk_value)
		{
			$this->query('delete from ' . $table . ' where '
				. $pk_field . ' = ' . $this->escape($pk_value));
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
	 * @return SQLiteClawDB return self (for fluent interfaces)
	 */
	public function update($table, $data = array(), $pk_value, $pk_field = 'id')
	{
		$sql = array();
		foreach ($data as $field => &$value)
		{
			$sql[] = $field . ' = \'' . sqlite_escape_string($value) . '\'';
		}
		$fields = implode(', ', array_keys($data));
		foreach ($data as $key => &$value)
		{
			$value = sqlite_escape_string($value);
		}
		$values = implode(', ', array_values($data));
		$this->query('update ' . $table . ' set ' . implode(', ', $sql)
			. ' where ' . $pk_field . ' = \''
			. sqlite_escape_string($pk_value) . '\'');
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
	public function &getArray($sql, $params = array('mode' => SQLITE_ASSOC))
	{
		$result = array();
		$this->query($sql);
		while (sqlite_valid($this->result))
		{
			$result[] = $this->fetch($params);
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
	public function &getRow($sql, $params = array('mode' => SQLITE_ASSOC))
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
		$result = $this->fetch(array('mode' => SQLITE_NUM));
		$result = array_pop($result);
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
	 * @return int affected rows
	 * @todo return affected rows
	 */
	public function query($sql, $params = array('mode' => SQLITE_ASSOC))
	{
		if (!($this->result = sqlite_query($sql, $this->db, $params['mode'])))
		{
			throw new DBClawException('Invalid SQL Query: ' . $sql .
				'(' . sqlite_error_string(sqlite_last_error($this->db)) . ')');
		}
		return sqlite_num_rows($this->result);
	}

	/**
	 * Fetches next result from result set that derrived from ClawDB::query
	 * Result must be at ClawDB::$result
	 *
	 * @param Array $params Array of parameters.. Can hold fetchmode
	 */
	public function fetch($params = array('mode' => MYSQL_ASSOC))
	{
		return sqlite_fetch_array($this->result, $params['mode']);
	}

	/**
	 * Releases the ClawDB::$result
	 * 
	 * @return SQLiteClawDB return self (for fluent interfaces)
	 */
	public function free()
	{
		$this->result = null;
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
		return sqlite_escape_string($value);
	}

	/**
	 * Returns last insert id
	 *
	 * @return mixed
	 */
	public function getLastInsertPK()
	{
		return sqlite_last_insert_rowid($this->db);
	}
}
?>