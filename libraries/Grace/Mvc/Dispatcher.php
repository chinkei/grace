<?php
uses('Grace_Routing_Mapper');

class Grace_Mvc_Dispatcher
{
	public function execute()
	{
		$mapper = new Grace_Routing_Mapper();

		// add a simple named route without params
		$mapper->addRoute('home', '');
		
		// add a complex named route
		$mapper->addRoute('read', '/blog/read/{:id}{:format}', array(
			    'findPattern' => array(
			        'id'     => '(\d+)',
			        'format' => '(\..+)?',
			    ),
			    'parameters' => array(
			        'controller' => 'blog',
			        'action'     => 'read',
			        'format'     => 'html',
			    ),
			)
		);
		$mapper->addRoute(null, '/{:module}/{:controller}/{:action}');
		
		$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		$path = rtrim($path, '/');
		$route = $mapper->match($path, $_SERVER);
		
		//print_r($mapper->getRoutes());
		
		if ( !$route ) {
		    // no route object was returned
		    echo "No application route was found for that URI path.";
		    exit();
		}
		
		// does the route indicate an action?
		if (isset($route->parameters['module'])) {
		    // take the action method directly from the route
		    $module = $route->parameters['module'];
		} else {
		    // use a default action
		    $module = 'home';
		}
		
		// does the route indicate a controller?
		if (isset($route->parameters['controller'])) {
			$ctrName = $route->parameters['controller'];
		} else {
		    $ctrName = 'index';
		}
		
		$controller = $module . '_' . $ctrName . '_ctr';
		
		// does the route indicate a controller?
		if (isset($route->parameters['action'])) {
		    // take the controller class directly from the route
		    $action = $route->parameters['action'] . '_action';
		} else {
		    // use a default controller
		    $action = 'main_action';
		}
		
		uses('Grace_Mvc_Contrl_Controller');
		uses('Grace_Mvc_Model_Model');
		
		include APP_PATH . '/module/' . $module . '/controller/' . $ctrName . '.php';
		$object   = new $controller;
		$callable = array($object, $action);
		
		if ( !is_callable($callable) ) {
			exit('不能调用');
		}
		
		// 执行方法
		call_user_func($callable);
	}
	
	public function dispatch($request, $response)
	{
		
	}
	
	public function _invoke()
	{
		
	}
	
	protected function _getController()
	{
		
	}
	
	protected function _loadController()
	{
		
	}
}
?>