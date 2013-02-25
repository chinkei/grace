<?php
class home_index_mdl extends Grace_Mvc_Model_Model
{
	public function getAll()
	{
		$sql = 'SELECT * ' .
		       'FROM ' . $this->_db->dbPrefix('company') . ' ';
		
		print_r($this->_db->query($sql)->resultArray());
		print_r($this->_db->query($sql)->rowArray());
		print_r($this->_db->query($sql)->colArray('company_id'));
	}
}
?>