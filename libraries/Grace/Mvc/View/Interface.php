<?php
interface Grace_Mvc_View_Interface
{
	public function assign($tpl_var, $value = '');
	public function fetch($filename, $cache_id = '');
	public function display($filename, $cache_id = '');
	public function setViewDir($path);
}
?>