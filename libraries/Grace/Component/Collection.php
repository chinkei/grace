<?php
abstract class Grace_Component_Collection
{
	protected $_instance = array();
	
	abstract public function add($name, $instance);
	
	abstract public function remove($name);
	
	abstract public function has($name);
}
?>