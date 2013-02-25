<?php
class GR_Hook
{

	private $_hooks = array();
	
	/**
	 * 监听事件
	 */
	private static $_listeners  = array();
	
	/**
	 * 事件优先级数
	 */
	private static $_priorities = 2;
	
	
	public function __construct()
	{
		// 加载框架路由Hook文件
		$file_path = APP_PATH.'/config/hook.php';
		if ( ! file_exists($file_path)) {
			exit('The hook configuration file does not exist.');
		}
		
		require($file_path);
		
		if ( ! isset($hook) || ! is_array($hook)) {
			exit('Your hook config file does not appear to be formatted correctly.');
		}
		
		// 设置Hook优先级数
		if (isset($hook['priorities'])) {
			self::setPriority($hook['priorities']);
			unset($hook['priorities']);
		}
		
		$this->_hooks = $hook;
		unset($hook);
	}
	
	/**
	 * 获取事件
	 *
	 * @param  String $event 事件名
	 * @return Mixed 
	 */
	public static function getEvent($event = NULL)
	{
		if (NULL === $event) {
			return self::$_listeners;
		}
		return self::is_exists($event) ? self::$_listeners[$event] : FALSE; 
	}
	
	/**
	 * 设置事件优先级
	 *
	 * @param  Int $event 事件名
	 * @return Bool 
	 */
	public static function setPriority($num)
	{
		if (is_numeric($num) && $num >= 0) {
			self::$_priorities = $num;
			return TRUE;
		}
		trigger_error('param $num is must > 0 ', E_USER_NOTICE);
		return FALSE;
	}
	
	/**
	 * 触发事件
	 * 
	 * @param  String $event 事件名
	 * @param  Mixed  $args  参数
	 * @return Bool
	 */
	public static function trigger($event, $args = NULL)
	{
		if ( ! self::is_exists($event)) {
			return FALSE;
		}
		$listeners = self::$_listeners[$event];
		
		for ($i = 0; $i <= self::$_priorities; $i++) {
			if (!isset($listeners[$i])) continue;
			$hooks = $listeners[$i];
			foreach($hooks as $hook){
				if (is_array($hook['args'])) {
					call_user_func_array($hook['handler'], $hook['args']);
				} else {
					call_user_func($hook['handler']);
				}
			}
		}
		return TRUE;
	}
	
	/**
	 * 载入当前模块Hook事件
	 */
	public function loadModuleHook()
	{
		// 路由为初始化时不能载入模块hook文件
		if ( ! class_exists('Router')) {
			// TODO
			exit('module hook must be after router is instanced');
		}
		
		$module = Router::getModule();
		
		// 加载框架路由Hook文件
		$file_path = APP_PATH.'/modules/'.$module.'/config/hook.php';
		if ( ! file_exists($file_path)) {
			return FALSE;
		}
		
		require($file_path);
		
		if ( ! isset($hook) || ! is_array($hook)) {
			exit('Your module hook config file does not appear to be formatted correctly.');
		}
		
		$this->_hooks = array_merge($this->_hooks, $hook);
		unset($hook);
	}
	
	/**
	 * 载入hook文件的事件监听
	 * 
	 * @param String $event 事件名
	 */
	public function addHookListener($event, $isModule = FALSE)
	{
		if ( !array_key_exists($event, $this->_hooks) ) {
			exit('the `' . $event . '` : is not exists !');
		}
		
		if ($isModule === TRUE) {
			// 路由为初始化时不能载入模块hook文件
			if ( ! class_exists('Router')) {
				// TODO
				exit('addHookListener must be after router is instanced');
			}
		}
		
		$_eventHook = $this->_hooks[$event];
		foreach ($_eventHook as $k => $v) {
			if ( !isset($v['class']) || !isset($v['func']) || !isset($v['fName']) || !isset($v['fPath']) ) {
				continue;
			}
			
			$filePath = APP_PATH . '/' . ($isModule ? 'modules/' . Router::getModule() . '/' : '') . $v['fPath'] . '/' . $v['fName'];
			if ( !file_exists($filePath) ) {
				continue;
			}
			
			require_once($filePath);
			
			if ( !is_callable(array($v['class'], $v['func'])) ) {
				continue;
			}
			self::addListener($event, array($v['class'], $v['func']), $v['params'], $k);
		}
		return TRUE;
	}
	
	/**
	 * 添加监听事件
	 * 
	 * @param  String   $event    事件名
	 * @param  Callback $handler  事件句柄
	 * @param  Array    $args     参数
	 * @param  Int      $priority 优先级
	 * @return Bool
	 */
	public static function addListener($event, $handler, $args = array(), $priority = 1)
	{
		if ($priority < 0 || $priority > self::$_priorities) {
			trigger_error('param $priority is must > 0 AND <= ' . self::$_priorities, E_USER_NOTICE);
			return FALSE;
		}
		
		if (is_callable($handler)) {
			$hook = array();
			$hook['handler'] = $handler;
			$hook['args']    = $args;
            self::$_listeners[$event][$priority][] = $hook;
            return TRUE;
        }
		return FALSE;
	}
	
	/**
	 * 移除监听事件
	 * 
	 * @param  String   $event    事件名
	 * @return Bool
	 */
	public static function removeEvents($event)
	{
		if (isset(self::$_listeners[$event])) {
			unset(self::$_listeners[$event]);
		}
		return TRUE;
	}
	
	/**
	 * 检测是否存在该监听事件
	 * 
	 * @param  String   $event    事件名
	 * @return Bool
	 */
	private static function is_exists($event)
    {
        return isset(self::$_listeners[$event]) ? TRUE : FALSE;
    } 
}
?>