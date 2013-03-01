<?php
class index_ctr extends Grace_Mvc_Contrl_Controller
{
	
	private $_view = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->_view = $this->load->view('template');
	}
	
	public function main_action()
	{
		$model = $this->load->model('index');
		$model->getAll();
		
		echo url_segments(1, TRUE);
		print_r($this->input->getParams());
		$this->_view->assign('name', 'caiyun');
		$this->_view->display('index.tpl');
	}
}
?>