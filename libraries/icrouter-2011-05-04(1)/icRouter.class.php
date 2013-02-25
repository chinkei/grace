<?php
/**
 * Router class. Holds collection of routes. Base class of icRouting
 *
 * @author     Igor Crevar <crewce@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php
 * @version    0.8
 */
class icRouter{
	/**
	 * @brief: Array collection of all route objects hashed by route name
	 *
	 * @var array of icRoute - key is route name
	 */
	protected $hashedRoutes = array();
	/**
	 * @brief: Array collection of all route objects
	 *
	 * @var array of icRoute
	 */
	protected $routes = array();
	
	
	/**
	 * @brief: Reference to matched icRoute object
	 *
	 * @var reference to icRoute
	 */
	protected $matchedRoute = NULL;
	
	/*
	 * set this to true if you want debuig output
	 */
	public static $DEBUG = 0; 
	
	public function __construct(){
		
	}
	
	/* Add route to router
	 * @param icRoute $newRoute reference to route object
	 */
	public function addRoute(icRoute& $newRoute){
		$this->hashedRoutes[ $newRoute->getName() ] = $newRoute;
		$this->routes[] = $newRoute;
	}
	
	/* Add routes to router
	 * @param array $newRoutes array if icRoutes
	 */
	public function addRoutes(array $newRoutes){
		foreach ($newRoutes as &$nr)
		{
			$this->hashedRoutes[ $nr->getName() ] = $nr;
			$this->routes[] = $nr;
		}
	}
	
	/* Try to find match for url
	 * also sets $this->matchedRoute which can be later retrieved
	 * @param string $url Url to match
	 * @return boolean true if route is matched
	 */
	public function match( $url ){
		$this->matchedRoute = NULL;
		foreach ($this->routes as &$route)
		{
			if ( $route->match($url) )
			{
				$this->matchedRoute = $route;
				return true;
			}	
		}
		return false;
	}
	
	/* Generates url depending on routeName and parameters
	 * @param string $routeName
	 * @param mixed $params - can be (key,value) array or string of url parameters
	 * @throws icException if route with $routeName does not exist
	 * @return string generated url
	 */
	public function generate($routeName, $params)
	{
		if ( !array_key_exists($routeName, $this->hashedRoutes) )
		{
			throw new icException( sprintf("Route with name %s does not exist", $routeName) );
		}
		//if $params is provided as string make array from it
		//this is very bad(although can be easier for developer). 
		//Preparing $params as array IS THE BEST CHOICE!!!
		if ( !is_array($params) )
		{
			$params = $this->stringToRouteParams($params);
		}
		$route = $this->hashedRoutes[ $routeName ];
		
		return $route->generate( $params );
	}
	
	/*
	 * Converts string to route params array
	 * @param string $v - string contains params key1=val1&key2=val2...
	 * @return array
	 */
	public function stringToRouteParams($v)
	{
		$keyValuePairs = explode( '&', $v );
		$params = array();
		foreach ( $keyValuePairs as $kvPair )
		{
			$kv = explode( '=', $kvPair, 2 );
			if ( count($kv) == 2 )
			{
				$params[ $kv[0] ] = $kv[1];
			}
		}
		return $params;
	}
	
	/* 
	 * Check if route with name $name exists
	 * @return boolean true or false
	 */
	public function isRouteExists($name)
	{
		return array_key_exists($name, $this->hashedRoutes);
	}
	
	/*
	 * Check if there is matched route object
	 */
	public function gotMatchedRoute()
	{
		return $this->matchedRoute instanceof icRoute;
	}
	
	/* Proxy for getMatchedParameters of matched route. Route must be matched before calling this!
	 * @param boolean $withoutDefaults
	 * if true default matched parameters are removed from matched parameters
	 */
	public function getParameters( $withoutDefaults = false ){
		return $this->matchedRoute->getMatchedParameters( $withoutDefaults );
	}
	
	public function setParam($name,$value){
		return $this->matchedRoute->setMatchedParam($name,$value);
	}
	
	
	public function getParam($name){
		return $this->matchedRoute->getMatchedParam($name);
	}
	
	public function removeParam($name){
		return $this->matchedRoute->removeMatchedParam($name);
	}
	
	/*
	 * returns matched route
	 * @return icRoute or null
	 */
	public function getMatchedRoute()
	{
		return $this->matchedRoute;
	}
	
	
	/*
	 * clears all routes
	 */
	public function clear()
	{
		$this->hashedRoutes = array();
		$this->routes = array();
		$this->matchedRoute = NULL;
	}
	
}