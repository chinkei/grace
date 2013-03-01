<?php
uses('Grace_Ioc_Ioc');
uses('Grace_Mvc_View_Interface');

class Grace_Mvc_View_Driver_Simple implements Grace_Mvc_View_Interface
{	
	public $view_dir  = '';
    public $cache_dir = '';
	
	protected $_tpl_var = array();
	
	public function __construct($module = FALSE)
	{
		if ( $module === FALSE ) {
			$this->template_dir = APP_PATH . '/module/' . $module.'/view';
		} else {
			$this->template_dir = APP_PATH . '/view';
		}
	}
	
	public function setViewDir($path)
	{
		$this->view_dir = $path;
	}
	
	public function assign($tpl_var, $value = '')
	{
		if (is_array($tpl_var)) {
			foreach ($tpl_var as $key => $val) {
				if ($key != '') {
					$this->_tpl_var[$key] = $val;
				}
			}
		} else {
			if ($tpl_var != '') {
				$this->_tpl_var[$tpl_var] = $value;
			}
		}
	}
	
	public function fetch($file, $cache_id = '')
	{
		Grace_Ioc_Ioc::resolve('output')->stopBuffer();
		
		ob_start();
		$this->display($file, $cache_id);
		$content = ob_get_contents();
		ob_end_clean();
		
		Grace_Ioc_Ioc::resolve('output')->startBuffer();
		return $content;
	}
	
	public function display($file, $cache_id = '')
	{
		if ( !empty($this->_tpl_var) ) {
            extract($this->_tpl_var);
        }
		include $this->view_dir . '/' . $file;
	}
}
?>