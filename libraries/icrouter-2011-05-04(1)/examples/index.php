<?php
$dir = dirname( dirname(__FILE__) ).'/';
require $dir.'icException.class.php';
require $dir.'icRouter.class.php';
require $dir.'icRouteParameter.class.php';
require $dir.'icRoute.class.php';

require $dir.'examples/icSimpleRequest.class.php'; //we need it for this example
$oRequest = new icSimpleRequest();

$oRouter = new icRouter();
		$oRouter->addRoutes( array(
			 
			new icRoute( 'photo', 
						 //url pattern
						  '/photo/:view'
						)
			,new icRoute( 'test', 
						  //* on the end means that additional parameters are allowed
						  '/test/:view/:id/*', 
						  //id is default 0 so /test/list is also possible
						  array( 'view' => 'list', 'id' => 0 )
						)
			,new icRoute( 'home', 
						  '/*', 							
						  array( 'module' => 'home', 'view' => 'list' )
						)	
						  			  
			,new icRoute( 'module_view', 
						  '/:module/:view', 
						  array( 'view' => 'list' ) 
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
		
		if ($oRouter->isRouteExists($params['module'].'_'.$params['view']))
		{
			return $oRequest->getRelativeUrlRoot().$oRouter->generate($params['module'].'_'.$params['view'], $params);
		}
		return $oRequest->getRelativeUrlRoot().$oRouter->generate('module_view', $params);
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
		'module=home&page=2',
		'module=photo&view=list',
		'module=photo&view=show&id=20&name=name', //additional parameters are not allowed for photo
		'module=test&view=list',
		'module=test&view=show&id=10',
		'module=test&view=show&id=10&delete=1',
		'module=video&view=list'
		);
	
	
		foreach ($links as $i => $link)
		{
			echo '<a href="'.url_for($link).'">Link for '.$link.'</a>';
			if ($i == 3) echo ' generated link is /photo/show because route doesnt allow extra parameters than /photo/:view!!';
			echo '&nbsp; &nbsp; <br />';
		}
		
	}
	catch (icException $e)
	{
		echo 'Error: '.$e->getMessage().'<br />';
	}
	
	
