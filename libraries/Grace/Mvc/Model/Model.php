<?php
abstract class Grace_Mvc_Model_Model
{	
	protected $_conrtol = NULL;
	protected $_db      = NULL;
	
	public function __construct()
	{
		$this->_db      = Grace_Database_Db::loadDriver();
		$this->_conrtol = get_instance();
	}
	
	public function __get($name)
    {
		if ($this->_conrtol != NULL && isset($this->_conrtol->{$name})) {
			return $this->_conrtol->{$name};
		}
		return FALSE;
    }
}
?>