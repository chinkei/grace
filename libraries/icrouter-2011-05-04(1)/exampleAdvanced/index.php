<?php
$dir = dirname( dirname(__FILE__) ).'/';
require $dir.'icException.class.php';
require $dir.'icRouter.class.php';
require $dir.'icRouteParameter.class.php';
require $dir.'icRoute.class.php';

require $dir.'examples/icSimpleRequest.class.php'; //we need it for this example
$oRequest = new icSimpleRequest();

$oRouter = new icRouter();
//icRouter::$DEBUG=1;
//define some route parameter types here
		$oRPWord = new icRouteParameter(icRouteParameter::REG_MATCH, '\w+' );
    	$oRPBool = new icRouteParameter(icRouteParameter::REG_MATCH, '[01]' );
		$oRPInt = new icRouteParameter(icRouteParameter::INT);
    	$pRPSlug = new icRouteParameter(icRouteParameter::REG_REPLACE, 'A-Za-z\-_0-9' );
    	$pRPNotIn = new icRouteParameter(icRouteParameter::NOT_IN, array('photo', 'test') );

//add some routes
//note. 4-th argument of icRiute constructor - you must define all parameters in url with exactly same order!!
//for example if url pattern looks like  '/photo/:action/:id-:name' 4-th argument
//will be something like array( 'action' => NULL, 'id' => NULL, 'name' => NULL)
		$oRouter->addRoutes( array(
			 
			new icRoute( 'photo', 
						 //url pattern
						  '/photo/:action/:id-:name/:delete',
						  //default parameter values 
						  array( 'module' => 'photo', 'action' => 'index', 'id' => 0, 'delete' => 0, 'name' => 'noname' ),
						  //description of every parameter
						  array( 'action' => $oRPWord, 'id' => $oRPInt, 'name' => $pRPSlug, 'delete' => $oRPBool) )
						 
			,new icRoute( 'test', 
						  //* on the end means that additional parameters are allowed
						  '/test/:action/static/:ref_id/:tag/*', 
						  array( 'module' => 'test', 'ref_id' => 0, 'tag' => 'no_tag' ), 
						  //specify parameters
						  array( 'action' => $oRPWord, 'ref_id' => $oRPInt, 'tag' => NULL ) ) 
						  
			,new icRoute( 'home', 
						  '/', 							
						  array( 'module' => 'home', 'action' => 'index' ), 
						  //define parameters on your own - order is important
						  array(),
						  //match pattern defined manually 
						  '#^/(?:page=(?P<page>[0-9]+))?$#'
						)	
						  			  
			,new icRoute( 'module_action', 
						  '/:module/:action/*', 
							array( 'action' => 'index' ), 
							array( 'module' => $pRPNotIn, 'action' => $oRPWord)
						 ) //additional params are allowed 
	));
								 

	//helper func
	function url_for($params)
	{
		global $oRouter, $oRequest;
		
		//convert parameters in string format to array format is necessary
		if (!is_array($params)) $params = $oRouter->stringToRouteParams($params);
		
		if ($oRouter->isRouteExists($params['module']))
		{
			return $oRequest->getRelativeUrlRoot().$oRouter->generate($params['module'], $params);
		}
		return $oRequest->getRelativeUrlRoot().$oRouter->generate('module_action', $params);
	}

	try 
	{
		if ( $oRouter->match( $oRequest->getPath()) )
		{
			echo 'Matched route with name: '.$oRouter->getMatchedRoute()->getName().'<br />';
			echo 'Parameters are: <br />';
			foreach ($oRouter->getParameters() as $key => $value)
			{
				echo $key.' = '.$value.'<br />';
			}
		}
		else
		{
			echo 'There is no match for this route!!!<br />';
		}
		
		$links = array(
		'module=home',
		'module=photo&action=list',
		'module=photo&action=show&id=20&name= whats my Name ',
		'module=photo&action=list&id=20&name= whats my Name &delete=1',
		'module=test&action=foo&ref_id=20',
		'module=test&action=bar&ref_id=30&p1=1&p2=2',
		'module=video&action=show&page=20'
		);
	
	
		foreach ($links as $link)
		{
			echo '<a href="'.url_for($link).'">Link for '.$link.'</a>';
			echo '&nbsp; &nbsp; <br />';
		}
		
	}
	catch (icException $e)
	{
		echo 'Error: '.$e->getMessage().'<br />';
	}
	
	
