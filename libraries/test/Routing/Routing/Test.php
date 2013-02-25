<?php
include 'Mapper.php';

$mapper = new Grace_Mvc_Routing_Mapper();

// add a simple named route without params
$mapper->addRoute('home', '/');

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
// add a simple unnamed route with params
$mapper->addRoute(null, '/{:module}/{:controller}/{:action}');

echo $_SERVER['PATH_INFO'];
// get the route based on the path and server
$route = $mapper->match($_SERVER['PATH_INFO'], $_SERVER);
//print_r($mapper->getRoutes());

if (! $route) {
    // no route object was returned
    echo "No application route was found for that URI path.";
    exit();
}

// does the route indicate a controller?
if (isset($route->parameters['controller'])) {
    // take the controller class directly from the route
    $controller = $route->parameters['controller'];
} else {
    // use a default controller
    $controller = 'Index';
}

// does the route indicate an action?
if (isset($route->parameters['action'])) {
    // take the action method directly from the route
    $action = $route->parameters['action'];
} else {
    // use a default action
    $action = 'index';
}

print_r($route->parameters);

exit();
?>