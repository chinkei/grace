<?php
class home_index_ctr extends Grace_Mvc_Contrl_Controller
{
	
	private $_view = NULL;
	
	public function __construct()
	{
		$this->_view = $this->load->view('home', 'template');
	}
	
	public function main_action()
	{	
		$model = $this->load->model('index', 'home');
		$model->getAll();
		
		$this->_view->assign('name', 'caiyun');
		$this->_view->display('index.tpl');
	}
}
?>