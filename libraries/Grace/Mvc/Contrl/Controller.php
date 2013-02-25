<?php
uses('Grace_Ioc_Ioc');
uses('Grace_Event_Listener');

abstract class Grace_Mvc_Contrl_Controller implements Grace_Event_Listener
{
	
	/**
	 * 当前控制器实例
	 * 
	 * @var object
	 */
	private static $_instance = NULL;
	
	/**
     * @var EventManager
     */
    protected $_event = NULL;
	
	/**
	 * 构造函数
	 *
	 * @return void
	 */
	public function __construct($component = array())
	{
		self::$_instance = $this;
		
		// 注册控制器事件
		$this->_event = Grace_Ioc_Ioc::resolve('event');
		$this->_event->attchSubscriber(self::$_instance);
	}
	
	/**
	 * 获取当前控制器实例
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		return self::$_instance;
	}
	
	public function getEventListeners()
	{
		return array(
            'Controller.initialize' => array('callable' => array($this, 'initialize')),
            'Controller.startup'    => array('callable' => array($this, 'startup')),
            'Controller.shutdown'   => array('callable' => array($this, 'shutdown')),
        );
	}
	
	public function startupProcess()
    {
        $this->_event->dispatch('Controller.initialize');
        $this->_event->dispatch('Controller.startup');
    }
	
	public function shutdownProcess()
    {
		$this->_event->dispatch('Controller.shutdown');
    }
	
	public function initialize()
	{
		
	}
	
	public function startup()
	{
		
	}
	
	public function shutdown()
	{
		
	}
	
	public function __get($name)
	{
		return Grace_Ioc_Ioc::resolve($name);
	}
}
?>