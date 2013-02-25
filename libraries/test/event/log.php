<?php
include 'Event/Event.php';
include 'Event/Listener.php';

class log implements Grace_Event_Listener
{
	public function a()
	{
		echo '0';
	}
	
	public function b()
	{
		echo '1';
	}
	
	public function c()
	{
		echo '2';
	}
	public function d()
	{
		echo '3';
	}
	public function e()
	{
		echo '4';
	}
	
	public function getEventListeners()
	{
		return array(
            'Controller.initialize' => array('callable' => array($this, 'a')),
            'Controller.startup' => array('callable' => array($this, 'b')),
            'Controller.beforeRender' => array('callable' => array($this, 'c')),
            'Controller.beforeRedirect' => array('callable' => array($this, 'd')),
            'Controller.shutdown' => array('callable' => array($this, 'e')),
        );
	}
}
$a = Grace_Event_Event::getInstance();
$a->attchSubscriber(new log());
$a->dispatch("Controller.initialize");
$a->dispatch("Controller.beforeRender");
$a->dispatch("Controller.startup");
$a->dispatch("Controller.shutdown");
$a->dispatch("Controller.beforeRedirect");
?>