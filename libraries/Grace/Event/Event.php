<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 事件管理类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Event
 */
class Grace_Event_Event
{
	/**
	 * 监听事件
	 */
	private $_listeners  = array();
	
	public function __construct()
	{
	}
	
	/**
     * 触发事件
     *
     * @param  string $evtName 事件名称
     * @return bool
     */
    public function dispatch($evtName)
    {
		$eventListeners = $this->_fetch_listeners($evtName);
		
        foreach ($eventListeners as $listener) {
            if ($listener['params'] !== FALSE && is_array($listener['params'])) {
				call_user_func_array($listener['callable'], $listener['params']);
            } else {
                call_user_func($listener['callable']);
            }
        }
		return TRUE;
    }
	
	/**
     * 取得事件监听列表
     *
     * @param  string $evtName
     * @return array
     */
    protected function _fetch_listeners($evtName)
    {
        if ( !isset($this->_listeners[$evtName]) || empty($this->_listeners[$evtName]) ) {
            return array();
        }
		
        ksort($this->_listeners[$evtName]);
        $result = array();
        foreach ($this->_listeners[$evtName] as $priority) {
            $result = array_merge($result, $priority);
        }
        return $result;
    }
	
	/**
	 * 添加事件
	 * 
	 * @param  string   $evtName  事件名称
	 * @param  callback $callable 回调方法
	 * @param  array    $options  参数数组(key:priority 优先级 key:params 方法参数)
	 * @return void
	 */
	public function attach($evtName, $callable, $options)
	{
		$default = array('priority' => 0, 'params' => FALSE);
		$options = array_merge($default, $options);
		
		if ( !is_callable($callable) ) {
			return FALSE;
		}
		
        $this->_listeners[$evtName][$options['priority']][] = array(
            'callable' => $callable,
            'params'   => $options['params']
        );
		return TRUE;
	}
	
	/**
	 * 对象实现事件
	 * 
	 * @param  object 事件对象(Grace_Event_Listener)
	 * @return void
	 */
	public function attchSubscriber($eventListener)
	{
		if ( ( $eventListener instanceof Grace_Event_Listener ) === FALSE) {
			trigger_error('事件对象未实现Grace_Event_Listener接口!');
			return FALSE;
		}
		
		foreach ($eventListener->getEventListeners() as $name => $event) {
		    $options  = array();
		    $callable = $event;
			
		    if ( is_array($event) ) {
				if ( !isset($event['callable']) ) {
					continue;
				}
		        list($callable, $options) = $this->_extractCallable($event, $eventListener);
		    }
			
		    if ( is_string($callable) ) {
		        $callable = array($eventListener, $event);
		    }
		    $this->attach($name, $callable, $options);
		}
	}
	
	/**
     * 移除事件监听
     *
     * @param  string $evtName 事件名称
     * @return bool
     */
    public function remove($evtName)
    {
        if ( !isset($this->_listeners[$evtName]) ) {
            return TRUE;
        }
		
		$this->_listeners[$evtName] = NULL;
		unset($this->_listeners[$evtName]);
		return TRUE;
    }
	
	/**
     * 移除事件订阅对象的所有事件方法
     *
     * @param  object $eventListener 事件订阅对象(Grace_Event_Listener)
     * @param  string $evtName       事件名称
     * @return bool
     */
    public function removeSubscriber($eventListener, $evtName = NULL)
    {
		if ( ( $eventListener instanceof Grace_Event_Listener ) === FALSE) {
			trigger_error('事件对象未实现Grace_Event_Listener接口!');
			return FALSE;
		}
		
        $events = $eventListener->implementedEvents();
		if ( !empty($evtName)) {
			if ( !isset($events[$evtName]) ) {
				return TRUE;
			}
			$events = array($evtName => $events[$evtName]);
		}
		
        foreach ($events as $name => $event) {
            if ( is_array($event) ) {
				if ( !isset($event['callable']) ) {
					continue;
				}
				$callable = array($eventListener, $event['callable']);
            }
			
			if ( is_string($callable) ) {
		        $callable = array($eventListener, $event);
		    }
            $this->_removeSubscriber($callable, $name);
        }
		return TRUE;
    }
	
    /**
	 * 提取callable和事件数组选项
	 * 
	 * @return array  $event  事件数组
	 * @return object $object 事件对象(Grace_Event_Listener)
	 * @return void
	 */
	protected function _extractCallable($event, $object)
	{
		$method  = $event['callable'];
        $options = $event;
        unset($options['callable']);
		
        if (is_string($method)) {
            $method = array($object, $method);
        }
        return array($method, $options);
	}
	
	/**
	 * 移除指定的事件的方法
	 * 
	 * @param  callback $callable 事件回调方法
	 * @param  string   $evtName  事件名称
	 * @return bool
	 */
	protected function _removeSubscriber($callable, $evtName)
	{
		if ( !isset($this->_listeners[$evtName]) ) {
            return TRUE;
        }
		
        foreach ($this->_listeners[$evtName] as $priority => $callables) {
            foreach ($callables as $key => $callback) {
                if ($callback['callable'] === $callable) {
                    unset($this->_listeners[$eventKey][$priority][$key]);
                    break;
                }
            }
        }
		return TRUE;
	}
}
?>