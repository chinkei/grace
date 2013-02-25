<?php
class Driver_Oracle extends Db
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
	
	
	public function conn_db($config)
	{
		if (FALSE === $config['pconnect']) {
			$this->_connect($config);
		} else {
			$this->_pconnect($config);
		}
	}
	
	private function _connect($cfg)
	{
		if ( ! function_exists('oci_connect') ) {
			throw new DBClawException('Oracle php extension not loaded');
		}
		
		if ( ! $this->conn = oci_connect($cfg['user'], $cfg['pass'], $cfg['db'], $cfg['charset']) )
		{
			$err = oci_error();
			throw new DBClawException(
				'Cannot connect to Oracle (' . $cfg['user'] . ', '
				. $err['message'] . ')');
		}
	}
	
	private function _pconnect()
	{
		if ( ! function_exists('oci_pconnect') ) {
			throw new DBClawException('Oracle php extension not loaded');
		}
		
		if ( ! $this->conn = oci_pconnect($cfg['user'], $cfg['pass'], $cfg['db'], $cfg['charset']) )
		{
			$err = oci_error();
			throw new DBClawException(
				'Cannot connect to Oracle (' . $cfg['user'] . ', '
				. $err['message'] . ')');
		}
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
	
	public function query($sql, $params = array())
	{
		$this->stmt = oci_parse($this->conn, $sql);

		if (!is_array($params))
		{
			$params = array();
		}
		foreach ($params as $k => &$v)
		{
			oci_bind_by_name($this->stmt, $k, $v);
		}

		$ok = oci_execute($this->stmt, OCI_COMMIT_ON_SUCCESS);

		if (!$ok)
		{
			$err = oci_error($this->stmt);
			throw new DBClawException('Invalid SQL Query: ' . $sql . ' ('
				. $err['message'] . ')');
		}
		return oci_num_rows($this->stmt);
	}
	
	public function db_close($mode = 'WR')
	{
		if ($mode == 'R+' || $mode == 'W+') {
			if ( isset($this->_conn_link[$mode]) ) {
				if (is_resource($this->_conn_link[$mode]) || is_object($this->_conn_link[$mode])) {
					oci_close($this->_conn_link[$mode]);
				}
				unset($this->_conn_link[$mode]);
			}
			return TRUE;
		}
		
		foreach ($this->_conn_link as $link) {
			if ( is_resource($link) || is_object($link) ) {
				oci_close($link);
			}
		}
		$this->_conn_link = array();
		return TRUE;
	}
}
?>