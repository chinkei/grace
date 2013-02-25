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
					sqlite_close($this->_conn_link[$mode]);
				}
				unset($this->_conn_link[$mode]);
			}
			return TRUE;
		}
		
		foreach ($this->_conn_link as $link) {
			if ( is_resource($link) || is_object($link) ) {
				sqlite_close($link);
			}
		}
		$this->_conn_link = array();
		return TRUE;
	}
}
?>