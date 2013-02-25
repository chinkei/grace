<?php
uses('Grace_Mvc_Routing_Mapper');

class Grace_Mvc_Routing_Dispatcher
{
	public function execute(Grace_IO_Input $input, Grace_IO_Output $output)
	{
		$mapper = new Grace_Mvc_Routing_Mapper();

		// add a simple named route without params
		$mapper->add('home', '/');
		
		// add a simple unnamed route with params
		$mapper->add(null, '/{:module}/{:controller}/{:action}');
		
		// add a complex named route
		$mapper->add('read', '/blog/read/{:id}{:format}', array(
			    'params' => array(
			        'id'     => '(\d+)',
			        'format' => '(\..+)?',
			    ),
			    'values' => array(
			        'controller' => 'blog',
			        'action'     => 'read',
			        'format'     => 'html',
			    ),
			)
		);
		
		// get the route based on the path and server
		$route = $mapper->match($_SERVER['PATH_INFO'], $_SERVER);
		
		if (! $route) {
		    // no route object was returned
		    echo "No application route was found for that URI path.";
		    exit();
		}
		
		// does the route indicate a controller?
		if (isset($route->values['controller'])) {
		    // take the controller class directly from the route
		    $controller = $route->values['controller'];
		} else {
		    // use a default controller
		    $controller = 'Index';
		}
		
		// does the route indicate an action?
		if (isset($route->values['action'])) {
		    // take the action method directly from the route
		    $action = $route->values['action'];
		} else {
		    // use a default action
		    $action = 'index';
		}
		
		print_r($route->values);
		
		exit();
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