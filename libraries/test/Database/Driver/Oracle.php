<?php
/**
 * PHP-Oracle Claw DB adapter
 *
 * @package claw.ext
 * @author Tomas Varaneckas <tomas [dot] varaneckas [at] gmail [dot] com>
 * @version $Id: OracleClawDB.php 145 2006-04-06 18:23:19Z metasite $
 */
class Grace_Database_Driver_Oracle extends Grace_Database_Driver
{
	/**
	 * Current result set..
	 *
	 * @var Oracle Statement
	 */
	protected $stmt = null;

	/**
	 * Last primary key
	 *
	 * @var int
	 */
	protected $last_pk = null;

	/**
	 * Oracle Connection
	 *
	 * @var Oracle connection resource
	 */
	protected $conn = null;

	/**
	 * Connects to database
	 *
	 * @param Array $cfg array('host', 'user', 'pass', 'db')
	 * @throws DBClawException when connection fails
	 */
	public function connect($conf)
	{
		if (!function_exists('oci_pconnect'))
		{
			throw new DBClawException('Oracle php extension not loaded');
		}
		if (!$this->conn = @oci_pconnect($conf['user'], $conf['pass'], $conf['db'], $conf['charset']))
		{
			$err = oci_error();
			throw new DBClawException(
				'Cannot connect to Oracle (' . $conf['user'] . ', '
				. $err['message'] . ')');
		}
	}

	/**
	 * Disconnector
	 */
	public function disconnect()
	{
		@oci_close($this->conn);
		$this->conn = null;
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
			. $pk_field . ' = :id', array('id' => $pk_value));
	}

	/**
	 * Define table and provide array of data to be inserted, this function
	 * does the operation
	 *
	 * @param string $table Table we are inserting data into
	 * @param Array $data Associative array of data to be inserted
	 */
	public function insert($table, $data = array())
	{
		$fields = implode(', ', array_keys($data));
		$bindings = array();
		foreach ($data as $key => &$value)
		{
			$bindings[] = ':' . $key;
		}
		$data['last_pk'] =& $this->last_pk;
		$this->query('insert into ' . $table . ' (' . $fields
			. ') values (' . implode(', ', $bindings) . ') '
			. 'returning ' . $pk_field . ' into :last_pk', $data);
	}

	public function delete($table, $pk_value, $pk_field = 'id')
	{
		if ($pk_value)
		{
			$this->query('delete from ' . $table . ' where '
				. $pk_field . ' = :id', array('id' => $pk_value));
		}
	}

	/**
	 * Update method, which is meant to modify ONE record BY primary key
	 *
	 * @param string $table Table we are updating
	 * @param Array $data Associative array of data to be updated
	 * @param mixed $pk_value Value of primary key of record we're updating
	 * @param string $pk_field Primary Key field (default - 'id'
	 */
	public function update($table, $data = array(), $pk_value, $pk_field = 'id')
	{
		$sql = array();
		foreach ($data as $field => &$value)
		{
			$sql[] = $field . ' = :' . $field;
		}
		$this->query('update ' . $table . ' set ' . implode(', ', $sql)
			. ' where ' . $pk_field . ' = :id', array('id' => $pk_value));
	}

	/**
	 * Gets array of arrays of rows with data from SQL query.
	 * Mind the portability!
	 *
	 * @param string $sql SQL query
	 * @param Array $params Array of query parameters. Can hold fetchmode
	 *		or variables that need to be bound..
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
	 *		or variables that need to be bound
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
	 *		or variables that need to be bound
	 */
	public function &getCell($sql, $params = array())
	{
		$this->query($sql, $params);
		$result = $this->fetch();
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
	 *		or variables that need to be bound
	 */
	public function query($sql, $params = array())
	{
		$this->stmt = @oci_parse($this->conn, $sql);

		if (!is_array($params))
		{
			$params = array();
		}
		foreach ($params as $k => &$v)
		{
			@oci_bind_by_name($this->stmt, $k, $v);
		}

		$ok = @oci_execute($this->stmt, OCI_COMMIT_ON_SUCCESS);

		if (!$ok)
		{
			$err = oci_error($this->stmt);
			throw new DBClawException('Invalid SQL Query: ' . $sql . ' ('
				. $err['message'] . ')');
		}
		return oci_num_rows($this->stmt);
	}

	/**
	 * Fetches next result from result set that derrived from ClawDB::query
	 * Result must be at ClawDB::$result
	 *
	 * @param Array $params Array of parameters.. Can hold fetchmode
	 */
	public function fetch($params = array('mode' => OCI_ASSOC))
	{
		return @oci_fetch_array($this->stmt, $params['mode']);
	}

	/**
	 * Releases the ClawDB::$result
	 */
	public function free()
	{
		@oci_free_statement($this->stmt);
	}

	/**
	 * Escaper. As it's oracle, no need for escaping...
	 *
	 * @param string $value not so clean value
	 * @return string clean value
	 */
	public function escape($value)
	{
		return $value;
	}

	/**
	 * Returns last insert id
	 *
	 * @return mixed
	 */
	public function getLastInsertPK()
	{
		return $this->last_pk;
	}
}
?>