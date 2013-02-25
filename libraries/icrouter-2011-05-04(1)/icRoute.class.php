<?php
/**
 * Describes route. Generates/matches urls
 *
 * @author     Igor Crevar <crewce@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php
 * @version    0.8
 */
class icRoute
{
	const ANY_KEY = '__any';
	const ANY_PATTERN = '(?P<__any>.*)';
	
	protected $findPattern;
	protected $createPattern;
	protected $base;
	/*
	 * route name must be descriptive, because it is using for creating urls...
	 */
	protected $name;
	protected $parameters;
	protected $defaultMatchedParameters;
	protected $matchedParameters;
	protected $allowAdditionalParameters;
	
	/*
	 * Name of parameters must contains only letters and numbers!
	 * @param string $name  name of route - something descriptive
	 * @param string $createPattern
	 * @param array $defaultMatchedParameters default values for parameters
	 * @param array $parameters - description of all parameters
	 * @param string $findPattern - NULL, FALSE, '' to compile it dynamicaly
	 * @param string $base - unmutable part of url(speed up for matching) - default is NULL
	 */
	public function __construct( $name, $createPattern, $defaultMatchedParameters = array(), 
								 $parameters = NULL, $findPattern = NULL, $base = NULL )
	{
		$this->name = $name;
		$this->defaultMatchedParameters = $defaultMatchedParameters;
		
		//if create pattern has * on end of string than it means that additional parameters are allowed
		$cpLen = strlen($createPattern);
		if ( $createPattern[$cpLen-1] == '*' )
		{
			$this->allowAdditionalParameters = true;
			//without last *, and / before * if its not begining of the string
			$createPattern = substr($createPattern, 0, $cpLen > 2 && $createPattern[$cpLen-2] == '/' ? -2 : -1); 
		}
		else
		{
			$this->allowAdditionalParameters = false;
		}
		$this->createPattern = $createPattern;
		
		$this->parameters = $parameters;
		
		$this->findPattern = $findPattern;		
		$this->base = $base;			
	}
	
	/*
	 * Check if this route can match provided url
	 * @params: $url string - Url to match
	 * @return: true if this route can match provided $url param
	 */
	public function match( $url )
	{
		//if we have static base compare with base first(faster comparation)
		if (!$this->base)
		{
			$this->extractBase();
		}
		
		if ( strpos($url, $this->base) !== 0 )
		{
			return false;
		}
		
		$this->extractParameters(); //extract parameters if they are not yet extracted(or passed)
		if ( !$this->findPattern ) //complie match pattern if not specified
		{
			$this->compileMatchPattern();
		}
		if ( preg_match( $this->findPattern, $url, $tMatchedParams ) )
		{
			$this->matchedParameters = array_merge( $this->defaultMatchedParameters, array() );
			
			if ( isset($tMatchedParams[self::ANY_KEY]) )
			{
				$this->parseAny($tMatchedParams[self::ANY_KEY]);
				unset($tMatchedParams[self::ANY_KEY]);
			}
			foreach ($tMatchedParams as $key => $value)
			{
				//skip no named matches
				if ( is_int($key) ) continue;
				$this->matchedParameters[ urldecode($key) ] = urldecode($value);
			}
			
			return true;
		}
		return false;
	}
	
	protected function parseAny($value)
	{
		//module, action and matched parameters
		//are allowed to be specified through __any parameter
		$notAllowed = array_keys($this->matchedParameters);
		//remove all characters after ?(and including) if ? exists	
		//this can produce errors if there is multiple ?, but that url is not valid, so we dont want to bother with it(same goes to & without ? in url)
		$startOfGetPos = strrpos( $value, '?' );
		if ( $startOfGetPos !== false )
		{
			$value = substr($value, 0, $startOfGetPos);
		}		
		$array = explode( '/', trim($value,'/') );
		$i = 0;
		while ($i + 1 < count($array) )
		{
			$key = urldecode($array[$i]);
			//skip if key is 0, null, '', or in not allowed parameter names
			if ( $key  &&  !in_array($key, $notAllowed) )
			{
				$this->matchedParameters[ $key ] = urldecode($array[$i+1]);
			}
			$i += 2;
		}
	}
	
	
	/*
	 * Generates url
	 * Iterates through parameters and replaces :parameter_name in createPattern with specified value
	 * if value is same as default parameter
	 * @param $params array of (key, value) pairs. Replace route parameters
	 * @return string generated url
	 */
	public function generate( $params )
	{	
		//extract parameters from createPattern if not specified
		$this->extractParameters();
			
		//put default parameters not specified in $params into $params 
		$mgParams = array_merge( $this->defaultMatchedParameters, $params);
		
		//this is code from symfony its better to check missing parameters imediatelly 
		if ( ($diff = array_diff_key($this->parameters, $mgParams)) )
        {
        	throw new icException(sprintf("Route with name %s, pattern %s has missing parameters (%s)", 
        				$this->name, $this->createPattern, implode(', ', array_keys($diff)) ));	
    	}
    	
    	//
		$tokens = array();
		$replaces = array();
		$url = $this->createPattern;
		$hasDefaults = false;
		
		//!IMPORTANT: order of parameters match order of parameters in $createPattern 
		foreach ( $this->parameters as $key => $icRouteParameterObject )
		{
			//throw exception if key is not in params
			//if ( !array_key_exists($key, $mgParams) ) throw new icException("$key is not specified for route $this->name");
			$value = $mgParams[$key];			
			
			// if parameter is last one and exactly the same value as value in defaultMatchedParameters
			// than just clear this parameter from generated url
			if ( !isset($this->defaultMatchedParameters[$key])  ||  $this->defaultMatchedParameters[$key] != $value )
			{
				//if there is route parameter object for this parameter, parse its value with routeParameterObject otherwise just do urlencode
				if ( !$hasDefaults )
				{
					$url = str_replace( ':'.$key
										, !($icRouteParameterObject instanceof icRouteParameter) ? urlencode( $value ) : 
																	  	   $icRouteParameterObject->parseValue( $value )
										,$url );
				
				}		
				else 						
				{
					$tokens[] = ':'.$key;
					$replaces[] = !($icRouteParameterObject instanceof icRouteParameter) ? urlencode( $value ) : 
																	$icRouteParameterObject->parseValue( $value );
					$url = str_replace( $tokens, $replaces, $url );
					$tokens = array();
					$replaces = array();
					$hasDefaults = false;					
				}
			}
			else
			{		
				$tokens[] = ':'.$key;
				$replaces[] = $value;
				$hasDefaults = true;
			}
		}
		
		//add additional at the end if route allows them
		if ( $this->allowAdditionalParameters )
		{
			$params = array_diff_key($params, array_merge($this->parameters, $this->defaultMatchedParameters));
			
			foreach ( $params as $key => $value )
			{
				$url .= '/'.urlencode($key).'/'.urlencode($value);
			}
		}
		
		//fix last parameter
		if ( $hasDefaults )
		{
			if ( !$this->allowAdditionalParameters  ||  !count($params) )
			{
				//substring to position of first default parameter
				$url = substr($url, 0, strpos($url, $tokens[0]) );
				return rtrim($url, '/');
			}
			else 
			{
				return str_replace( $tokens, $replaces, $url );
			}
		}
		
		return $url;
	}
	
	
	/*
	 * Returns matched parameters (or matched parameters without default matches)
	 * @param  boolean $withoutDefaults
	 * @return array key, value 
	 */
	public function getMatchedParameters( $withoutDefaults = false ){
		if ( !$withoutDefaults )
		{
			return $this->matchedParameters;	
		}
		$tMatArray = array_merge( $this->matchedParameters, array() );
		//remove all paremeters which are default
		foreach ($this->defaultMatchedParameters as $key => $value)
		{
			if ( $tMatArray[$key] == $value )
			{
				unset( $tMatArray[$key] );
			}
		}
		return $tMatArray;
	}
	
	public function getMatchedParam($name){
		return array_key_exists($name, $this->matchedParameters) ? $this->matchedParameters[$name] : NULL;
	}
	
	public function setMatchedParam($name, $value){
		$this->matchedParameters[$name] = $value;
	}
	
	public function removeMatchedParam($name)
	{
		unset( $this->matchedParameters[$name] );
	}
	
	public function getName(){
		return $this->name;
	}
	
	/* extract parameters from $this->createPattern
	 * if this->parameters is not specified
	 */
	private function extractParameters()
	{
		if ($this->parameters) return;
		preg_match_all( '/\:([A-Za-z0-9_]+)/', $this->createPattern, $matches );
		$this->parameters = array_flip($matches[1]);
	}
	
	/* extract unmutable base from $this->createPattern
	 */
	private function extractBase()
	{
		$pos = strpos( $this->createPattern, ':' );
		$this->base = $pos !== false ? substr(  $this->createPattern, 0, $pos ) : $this->createPattern;	
	}
	
	/*  compiles find(match) regular expression pattern from create pattern string
	 */
	private function compileMatchPattern()
	{
		$defaultReplaces = array();
		$hasDefaults = false;
		
		$findPattern = '';
		$parts = explode('/', trim($this->createPattern, '/') );
		foreach ($parts as $part)
		{
			//in one part of url we can have more than one key /:id-:name
			preg_match_all( '/\:([A-Za-z0-9_]+)/', $part, $matches );
			$matches = $matches[1];			
			
			if ( count($matches) )
			{				
				$tokens = array();
				$replaces = array();
				$isInDefault = true;
				foreach ($matches as $key)
				{
					$patternPart = isset($this->parameters[$key]) && $this->parameters[$key] instanceof icRouteParameter
						  		   ? $this->parameters[$key]->getPattern() 
						  		   : '[^/]+';
					$replaces[] = '(?P<'.$key.'>'.$patternPart.')';
					$tokens[] = ':'.$key; 
					$isInDefault = $isInDefault && array_key_exists($key, $this->defaultMatchedParameters);
				}
				
				$part = str_replace($tokens, $replaces, $part);
				
				if ( !$isInDefault )
				{
					if (!$hasDefaults)
					{
						$findPattern .= '/'.$part;
					}
					else 
					{
						$findPattern .= '/'.join('/', $defaultReplaces).'/'.$part;
						$defaultReplaces = array();
						$hasDefaults = false;
					}
				}
				else 
				{
					$defaultReplaces[] = $part;
					$hasDefaults = true;
				}
			}
			else if ($hasDefaults)
			{
				$defaultReplaces[] = $part;
			}
			else 
			{
				$findPattern .= '/'.$part;
			}
			
		}
		//adds sufix
		if ( !$hasDefaults )
		{
			if ( $this->allowAdditionalParameters )
			{
				$findPattern .= icRoute::ANY_PATTERN;
			}
			else 
			{
				$findPattern .= '/?';
			}
		}
		else 
		{			
			$patternSuffix = $this->allowAdditionalParameters ? icRoute::ANY_PATTERN : '/?';
			$i = count($defaultReplaces)-1;
			while ( $i >= 0 )
			{
				$patternSuffix = '(?:/'.$defaultReplaces[$i].$patternSuffix.')?';
				$i--;
			}
			
			$findPattern .= $patternSuffix.'/?';
			
		}
				
		//if ( icRouter::$DEBUG ) echo htmlspecialchars('#^'.$findPattern.'$#').'<br />';
		$this->findPattern = '#^'.$findPattern.'$#';
	}
	
}