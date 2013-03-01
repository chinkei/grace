<?php
uses('admin_base', APP_PATH . '/module');

class admin_index_ctr extends admin_base
{
	public function main_action()
	{
		$this->_view->assign('base_url', 'http://10.1.16.137/hmvc/www/hmvc/public');
		$this->_view->display('index.tpl');
	}
}
?>