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
		if ( ! function_exists('mysql_connect') ) {
			
		}
		
		if ( ! mysql_connect($cfg['host'], $cfg['user'], $cfg['pass']) ) {
			
		}
		
		if ( ! empty($cfg['db']) ) {
			$tshi->db->select($cfg['db']);
		}
	}
	
	public function insert($table, $data = array())
	{
		foreach ($data as &$value) {
			$value = $this->escape($value);
		}
		
		$fields = implode('", "', array_keys($data));
		$values = implode(', ', array_values($data));
		
		$sql = 'INSERT INTO "' . $table . '" ("' . $fields . '") VALUES (' . $values . ')';
		return $this->query($sql);
	}
	
	public function update($table, $data = array(), $pk_value, $pk_field = 'id')
	{
		
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
	
	private function _pconnect()
	{
		
	}
	
	public function db_select()
	{
		
	}
	
	public function db_set_charset()
	{
		
	}
	
	public function get_version()
	{
		
	}
	
	public function db_close($mode = 'WR')
	{
		if ($mode == 'R+' || $mode == 'W+') {
			if ( isset($this->_conn_link[$mode]) ) {
				if (is_resource($this->_conn_link[$mode]) || is_object($this->_conn_link[$mode])) {
					pg_close($this->_conn_link[$mode]);
				}
				unset($this->_conn_link[$mode]);
			}
			return TRUE;
		}
		
		foreach ($this->_conn_link as $link) {
			if ( is_resource($link) || is_object($link) ) {
				pg_close($link);
			}
		}
		$this->_conn_link = array();
		return TRUE;
	}
}
?>