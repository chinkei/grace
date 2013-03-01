<?php
class admin_base extends Grace_Mvc_Contrl_Controller
{
	protected $_view = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->_view = $this->load->view('template', 'admin');
	}
}
?>