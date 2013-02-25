<?php
$dir = dirname( dirname(__FILE__) ).'/';
require $dir.'icException.class.php';
require $dir.'icRouter.class.php';
require $dir.'icRouteParameter.class.php';
require $dir.'icRoute.class.php';

require_once 'PHPUnit.php';

class icRoutingTest extends PHPUnit_TestCase
{
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	protected function matchUrlOk($url, $params)
	{
		$this->oRouter->match($url);
        if ( !$this->oRouter->gotMatchedRoute() )
        {
        	$this->assertEquals(1, 0);
        }
        else 
        {
			$rParams = $this->oRouter->getParameters();
			$this->assertEquals(count($params), count($rParams));
			foreach ($params as $key => $val)
			{
				if ( !isset($rParams[$key]) )
				{	
					//$this->testTest($url, $params);
					$this->assertEquals('', $key);
				}
				if ( $rParams[$key] != $val ) 
				{
					//$this->testTest($url, $params);
					$this->assertEquals($rParams[$key], $val);
				}
			}
        	$this->assertEquals( 1, 1 );
    	    
 	     }
   }
	
	//:) test the test
	protected function testTest($url, $params)
	{
		echo $this->oRouter->getMatchedRoute()->getName().' '.$url.' ';print_r($this->oRouter->getParameters()); print_r($params);echo '<br /><br />';
	}
	
 	protected function genUrlExcpetion($name, $params)
	{
		try {
			$this->oRouter->generate($name, $params);
			$this->assertEquals(1,0);
		}
		catch(Exception $e)
		{
			 $this->assertEquals(1,1);
		}
 	}

 	public function test1()
    {
    	$url = '/photo/delete/10-sevap/1';    	
    	$params = array( 'module' => 'photo', 'action' => 'delete', 'id' => 10, 'name' => 'sevap', 'delete' => 1 );
    	$this->matchUrlOk($url, $params);
    }
    
    public function test2()
    {
    	$url = '/photo/show/10-inter';    	
    	$params = array( 'module' => 'photo', 'action' => 'show', 'id' => 10, 'name' => 'inter', 'delete' => 0 );
    	$this->matchUrlOk($url, $params);
    }
    
    public function test3()
    {
    	$url = '/photo/list';    	
    	$params = array( 'module' => 'photo', 'action' => 'list', 'id' => 0, 'name' => '', 'delete' => 0 );
    	$this->matchUrlOk($url, $params);
    }
    
	public function test4()
    {
    	$url = '/';    	
    	$params = array( 'module' => 'photo', 'action' => 'index');
    	$this->matchUrlOk($url, $params);
    }
    
	public function test5()
    {
    	$url = '/test/actiononame';    	
    	$params = array( 'module' => 'user', 'action' => 'actiononame', 'ref_id' => 0, 'tag' => 'no_tag');
    	$this->matchUrlOk($url, $params);
    }
    
	public function test6()
    {
    	//not in doesnt allow name*
    	$url = '/0test/actiontest/p_1/1/p_2/2';    	
    	$params = array( 'module' => '0test', 'action' => 'actiontest', 'p_1' => '1', 'p_2' => '2');
    	$this->matchUrlOk($url, $params);
    }
    
	public function test7()
    {
    	$url = '/video/show/100-videoname/p_1/value';    	
    	$params = array( 'module' => 'video', 'action' => 'show', 'id' => '100', 'name' => 'videoname',  'p_1' => 'value' );
    	$this->matchUrlOk($url, $params);
    }
    
	public function test8()
    {
    	$url = '/user/test/important/20/basic/30/tag1';
    	$params = array( 'module' => 'user', 'action' => 'test', 'ref_id' => 20, 'tag1' => 'tag1',
    					 'session_id' => 30, 'tag2' => 'no_tag' );
    	$this->matchUrlOk($url, $params);
    }
    
	public function test9()
    {
    	$url = '/test/list/ref100';    	
    	$params =  array( 'module' => 'user', 'action' => 'list', 'ref_id' => 'ref100', 'tag' => 'no_tag' );
    	$this->matchUrlOk($url, $params);
    }
    
	public function test10()
    {
    	$url = '/test/list/30/static/hopa';    	
    	$params =  array( 'module' => 'user', 'action' => 'list', 'ref_id' => 30, 'tag' => 'hopa' );
    	$this->matchUrlOk($url, $params);
    }
    
	public function test11()
    {
    	$url = '/user/some/important/10/basic/200/hopa/cupa/p_1/22';    	
    	$params =  array( 'module' => 'user', 'session_id' => 200, 'action' => 'some', 'ref_id' => 10, 
    					  'tag1' => 'hopa', 'tag2' => 'cupa', 'p_1' => '22' );
    	$this->matchUrlOk($url, $params);
    }
    
	public function test12()
    {
    	$url = '/user/some/important/bad/basic/200/hopa/cupa/p_1/22';    	
    	$this->oRouter->match($url);
        $this->assertFalse( $this->oRouter->gotMatchedRoute(), true );
    }
    
    
	public function test13()
    {
    	$params = array('module' => 'step', 'action' => 'help', 'p_1' => 1);
    	$url = $this->oRouter->generate('module_action', $params);
    	$this->assertEquals($url, '/step/help/p_1/1');
    }
	
    public function test14()
    {
    	$url = $this->oRouter->generate('photo', 'action=show&id=10&name= HELGA ## THE KING ');
    	$this->assertEquals($url, '/photo/show/10-helga-the-king');
    }
    
	public function test15()
    {
    	$url = $this->oRouter->generate('photo', 'module=photo&action=index');
    	$this->assertEquals($url, '/photo');
    }
    
	public function test16()
    {
    	$url = $this->oRouter->generate('photo', '');
    	$this->assertEquals($url, '/photo');
    }
    
	public function test17()
    {
    	$url = $this->oRouter->generate('video', array('id' => 10, 'name' => ' Hey ho ', 'action' => 'show', 'additional' => 77));
    	$this->assertEquals($url, '/video/show/10-hey-ho/additional/77'); 
    }
    
	public function test18()
    {
    	$url = $this->oRouter->generate('photo', 'action=index&id=10&name=test&delete=1');
    	$this->assertEquals($url, '/photo/index/10-test/1');
    }
    
	public function test19()
    {
    	$url = $this->oRouter->generate('home', '');
    	$this->assertEquals($url, '/');
    }
    
	public function test20()
    {
    	$url = $this->oRouter->generate('user', 'action=list&ref_id=20&session_id=30&tag1=30');
    	$this->assertEquals($url, '/user/list/important/20/basic/30/30');
    }
    
	public function test21()
    {
    	$url = $this->oRouter->generate('module_action', 'module=foo&action=bar&p1=1&p2=2');
    	$this->assertEquals($url, '/foo/bar/p1/1/p2/2');
    }
    
    public function test22()
    {
    	$url = $this->oRouter->generate('test', 'module=test&action=foobar');
    	$this->assertEquals($url, '/test/foobar');
    }
    
  	public function test23()
    {
    // '/test/:action/:ref_id/static/:tag',  'ref_id' => 0, 'tag' => 'no_tag'
    	$url = $this->oRouter->generate('test', 'action=foobar&ref_id=bar');
    	//ref_id must be int parsed intval(bar) = 0
    	$this->assertEquals('/test/foobar', $url);
    }
    
  	public function test24()
    {
    // '/test/:action/:ref_id/static/:tag',  'ref_id' => 0, 'tag' => 'no_tag'
    	$url = $this->oRouter->generate('test', 'action=foobar&ref_id=123');
    	$this->assertEquals('/test/foobar/123/static', $url);
    }
    
  	public function test25()
    {
    	$url = $this->oRouter->generate('test', 'action=foobar&ref_id=99&tag=foo&p1=1');
    	$this->assertEquals($url, '/test/foobar/99/static/foo');
    }
   	protected $oRouter;
 
    public function setUp()
    {
    	//creates some routes
    	//icRouter::$DEBUG = 1;
    	$this->oRouter = new icRouter();
    	$oRPWord = new icRouteParameter(icRouteParameter::REG_MATCH, '\w+' );
    	$oRPBool = new icRouteParameter(icRouteParameter::REG_MATCH, '[01]' );
		$oRPInt = new icRouteParameter(icRouteParameter::INT);
    	$pRPSlug = new icRouteParameter(icRouteParameter::REG_REPLACE, 'A-Za-z\-_0-9' );
    	$pRPNotIn = new icRouteParameter(icRouteParameter::NOT_IN, array('photo', 'test', 'user') );


		$this->oRouter->addRoutes( array(
			 
			new icRoute( 'photo', 
						  '/photo/:action/:id-:name/:delete', 
						  array( 'module' => 'photo', 'action' => 'index', 'id' => 0, 'delete' => 0, 'name' => '' ), 
						  array( 'action' => $oRPWord, 'id' => $oRPInt, 'name' => $pRPSlug, 'delete' => $oRPBool) 
						 ) 
						  
			,new icRoute( 'video', 
						  '/video/:action/:id-:name/*', 
						  array( 'module' => 'video', 'action' => 'index' ), 
						  array( 'action' => $oRPWord, 'id' => $oRPInt, 'name' => $pRPSlug) 
						) //additional params are allowed 
		
			,new icRoute( 'test', 
						  '/test/:action/:ref_id/static/:tag', 
						  array( 'module' => 'user', 'ref_id' => 0, 'tag' => 'no_tag' ) 
						) //additional params are allowed
						  
			,new icRoute( 'user', 
						  '/user/:action/important/:ref_id/basic/:session_id/:tag1/:tag2/*', 
						  array( 'module' => 'user', 'action' => 'index', 'ref_id' => 0, 'session_id' => 0, 'tag2' => 'no_tag' ), 
						  array( 'action' => $oRPWord, 'ref_id' => $oRPInt, 'session_id' => $oRPInt, 
						  		 'tag1' => NULL, 'tag2' => $oRPWord) 
						) //additional params are allowed 				  

			,new icRoute( 'home', 
						  '/', 
						  array( 'module' => 'photo', 'action' => 'index' )
						)	
						  			  
			,new icRoute( 'module_action', 
						  '/:module/:action/*', 
						  array( 'action' => 'index' ), 
						  array( 'module' => $pRPNotIn, 'action' => $oRPWord)
						 ) //additional params are allowed 	

							  
																				
		));
		//var_export($this->oRouter);
    }
 
    public function tearDown()
    {
    	$this->oRouter->clear();
    }
}


$suite = new PHPUnit_TestSuite();
$allMethods = get_class_methods('icRoutingTest');
foreach ($allMethods as $i => $method)
{
	if ( strpos($method, 'test') === 0 )
	{
		$suite->addTest( new icRoutingTest($method) );
	}
}
$result = PHPUnit::run($suite);
print $result->toHTML();