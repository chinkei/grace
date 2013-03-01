<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

uses('Grace_Routing_Mapper');
uses('Grace_Mvc_Contrl_Controller');
uses('Grace_Mvc_Model_Model');
uses('Grace_IO_Input');
uses('Grace_IO_Output');

/**
 * MVC调度器类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Mvc
 */
class Grace_Mvc_Dispatcher
{
	
	protected $_route = NULL;
	protected $_input = NULL;
	
	public function execute(Grace_IO_Input $input,  Grace_IO_Output $output)
	{
		$mapper = new Grace_Routing_Mapper();

		// 添加默认路由
		$mapper->addRoute('home', '');
		
		$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		$path = rtrim($path, '/');
		
		$route = $mapper->match($path, $_SERVER);
		
		//print_r($mapper->getRoutes());
		
		if ( !$route ) {
		    echo "No application route was found for that URI path.";
		    exit();
		}
		
		$this->_input = $input;
		$this->_route = $route;
		
		$params = $this->_getParams();
		$this->_execute($params);
	}
	
	protected function _execute($params)
	{
		$strPath = APP_PATH . '/controller';
		
		// 如果只有模块名则使用默认控制器
		if (count($params) == 0) {
			$params[] = 'index';
		}
		
		// 是否是HMVC模式
		if ( IS_HMVC === TRUE ) {
			$module  = (isset($this->_route->parameters['module'])) ? $this->_route->parameters['module'] : 'home';
			// 设置控制参数值
			$this->_input->setModule($module);
			$strPath = APP_PATH . '/module/' . $module . '/controller';
		}
		
		$i      = 0;
		$flg    = FALSE;
		$prefix = $module;
		foreach ($params as $key => $v) {
			$strPath .= '/' . $v;
			$i++;
			if ( is_file($strPath . '.php') ) {
				include $strPath . '.php';
				$flg = TRUE;
				break;
			}
			$prefix = $v;
		}
		
		$strContrl = $v;
		
		// 未找到控制器文件
		// 404
		if ( $flg === FALSE ) {
			if ( is_dir($strPath) && is_file($strPath . '/index.php')) {
				include $strPath . '/index.php';
				$strContrl = 'index';
				$params[]  = 'main';
			} else {
				exit('404');
			}
		}
		
		$ctrClass = $prefix . '_' . $strContrl . '_ctr';
		$params   = array_slice($params, $i);
	
		// 设置控制参数值
		$this->_input->setContrl($strContrl);
		
		$action = array_shift($params);
		
		// 设置action参数值
		$action = ( $action !== NULL ? $action : 'main');
		$this->_input->setAction($action);
		$method = $action . '_action';
		
		$object   = new $ctrClass;
		$callable = array($object, $method);
		if ( !is_callable($callable) ) {
			exit('不能调用');
		}
		
		// 执行方法
		if ( !empty($params) ) {
			call_user_func_array($callable, $params);
		} else {
			call_user_func($callable);
		}
	}
	
	protected function _getControllerClass()
	{
		// 获取控制器参数值
		$ctrName = (isset($this->_route->parameters['controller'])) ? $this->_route->parameters['controller'] : 'index';
		// 设置控制参数值
		$this->_input->setContrl($ctrName);
		
		// 是否是HMVC模式
		if ( IS_HMVC === TRUE ) {
			$module = (isset($this->_route->parameters['module'])) ? $this->_route->parameters['module'] : 'home';
			
			// 设置module参数值
			$this->_input->setModule($module);
			
			include APP_PATH . '/module/' . $module . '/controller/' . $ctrName . '.php';
			return $controller = $module . '_' . $ctrName . '_ctr';
		}
		
		include APP_PATH . '/controller/' . $ctrName . '.php';
		return $ctrName . '_ctr';
	}
	
	protected function _getAction()
	{
		// 是否存在action参数
		$action = (isset($this->_route->parameters['action'])) ? $this->_route->parameters['action'] : 'main';
		// 设置action参数值
		$this->_input->setAction($action);
		
		return $action . '_action';
	}
	
	protected function _getParams()
	{
		$params = ( isset($this->_route->parameters['*']) ) ? $this->_route->parameters['*'] : array();
		// 设置其他路径参数值
		$this->_input->setParams($params);
		
		return $params;
	}
}
?>