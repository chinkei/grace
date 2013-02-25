<?php
//dont care about IIS
//much of code is based on Joomla and symfony 
//with some differences
//request is not full rest request, just deals with GET AND POST, two most usual requests
// parameters are not parsed if they are not used - means parsing is done only on getInt/getString/etc
class icSimpleRequest{
	protected $requestMethod;
	//relative url to executed script(root)
	protected $relativeUrlRoot;
	protected $urlPrefix;
	protected $secureUrlPrefix;
	protected $normalUrlPrefix;
	
	protected $isSecure;
	protected $magicQuotes;
	
	protected $shouldIEscapeStringValues;
	//@var array - php $_FILES is messy and need to be in this way
	protected $fixedFilesArray;	
	//@var boolean - tell us if mod rewrite active or not - you better be sure it is :)
	private $_isModeRewriteActive = false;
	//@var boolean - name of script executed
    private $_scriptName = false;
 	//@var string prefix when taking path from current url. Prefix will be removed from url(this
 	//can be useful when for example we have language in front of every url
    private $_pathUrlPrefix = '';
	
	public function __construct()
	{
		$this->isSecure = NULL;
		$this->relativeUrlRoot = NULL;
		$this->urlPrefix = $this->secureUrlPrefix = $this->normalUrlPrefix = NULL;
		$this->requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET'; //default method
		$this->magicQuotes = get_magic_quotes_gpc();
	}
	
	/*
	public function injectRouter(icRouter& $router)
	{
		$this->router = $router;
		if ( $router->gotMatchedRoute() )
		{
			$this->setGetParameters( $router->getParameters() );
		}
	}
	*/
	/*
	 * typicaly called to set router parameters
	 * WARNING: paremeters inside array are not escaped!!!
	 */
	public function setGetParameters( $array )
	{
		foreach ($array as $key => $element)
		{
			$_GET[ $key ] = $element;
			//remove this key from post if exist
			/*
			if ( isset($_POST[$key]) )
			{
				unset( $_POST[$key] );
			}
			*/
		}
	}
	
	public function clear( $get = true, $pos = true )
	{
		if ( $get ) $_GET = array();
		if ( $post ) $_POST = array();
	}
	
	protected function getValue($name, $method = null)
	{
		if ( $method == NULL )
		{
			//if we didnt specified method try both $_POST AND $_GET
			//but first try from requested method
			$method = $this->requestMethod == 'GET' ? $_GET : $_POST;	
			if ( isset($method[$name]) )
			{
				return $method[$name];
			}
			$secondMethod = $method == 'GET' ? $_POST : $_GET;
			if ( isset($secondMethod[$name]) )
			{
				return $secondMethod[$name];
			}
			return null;
		}
		$method = $method == 'GET' ? $_GET : $_POST;
		if ( isset($method[$name]) )
		{
			return $method[$name];
		}
		//not defined
		return null;
	}
	
	public function getVar($name, $defaultValue = null, $method = NULL )
	{
		$rv = $this->getValue($name, $method);
		return !is_null($rv) ? $rv : $defaultValue;
	}
	public function getInt($name, $defaultValue = 0, $method = NULL )
	{
		$rv = $this->getValue($name, $method);
		return !is_null($rv) ? intval($rv) : $defaultValue;
	}
	
	public function getFloat($name, $defaultValue = 0, $method = NULL )
	{
		$rv = $this->getValue($name, $method);
		return !is_null($rv) ? doubleval($rv) : $defaultValue;
	}
	
	public function getString($name, $defaultValue = NULL, $escape = false, $method = NULL )
	{
		$value = $this->getValue($name, $method);
		if ( is_null($value) )
		{
			return $defaultValue;
		}
		if ( $this->magicQuotes )
		{
			$value = icUtil::stripslashes($value);
		}
		if ( $escape )
		{
			$value = icUtil::escape( $value );		
			$value = icUtil::filterXSS( $value );
		}
		return $value;
	}
	
	function getWord($name, $defaultValue = '', $pattern = '/^[A-Za-z0-9]*$/', $method = NULL )
	{
		$value = $this->getValue($name, $method);
		if ( is_null($value) )
		{
			return $defaultValue;
		}
		if ( $this->magicQuotes )
		{
			$value = icUtil::stripslashes($value);
		}
		if ( preg_match($pattern, $value) )
		{
			return $value;
		}
		return $defaultValue;
	}
	
	public function getArray($name, $defaultValue = false, $method = NULL )
	{
		$value = $this->getValue($name, $method);
		return !is_null($value) &&  is_array($value) ? $value : $defaultValue;
	}
	
	public function getFiles($key)
	{
		if ( !$this->fixedFilesArray  && count($_FILES) )
		{
			$this->fixedFilesArray = array();
			foreach ( $_FILES as $key => $val )
			{
				$tmp = array();
				foreach ( $val as $pFieldName => $pFieldValue )
				{
					if ( is_array($pFieldValue) )
					{
						foreach ( $pFieldValue as $fieldName => $fieldValue )
						{
							if ( !isset($tmp[$fieldName]) )
							{
								$tmp[$fieldName] = array();
							}
							$tmp[$fieldName][$pFieldName] = $fieldValue;
						}
					}
					else 
					{
						$tmp[$pFieldName] = $pFieldValue;
					}
				}
				$this->fixedFilesArray[$key] = $tmp;
			}
		}
		return isset($this->fixedFilesArray[$key]) ? $this->fixedFilesArray[$key] : array();
	}
	
	
	public function getMethod()
	{
		return $this->requestMethod;
	}
	
	public function setMethod($method) //can be used in forwarding
	{
		$this->requestMethod = $method;
	}
	
	public function setRelativeUrlRoot($val)
	{
		$this->relativeUrlRoot = $val;
	}
	
	public function getRelativeUrlRoot()
	{
		if ( is_null($this->relativeUrlRoot) )
		{
			$this->relativeUrlRoot = $this->findRelativeUrlRoot();
		}
		return $this->relativeUrlRoot;
	}
	
	//calculates relative url root
	public function findRelativeUrlRoot()
	{
		$scriptName = !empty($_SERVER['SCRIPT_NAME']) ? 
				$_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_SCRIPT_NAME'];
		return rtrim( dirname( $scriptName ), '/\\');
	}
	
	public function getRemoteAddress()
	{
    	return $_SERVER['REMOTE_ADDR'];
	}
	
	//symfony isSecure func
	public function isSecure()
	{
    	if (is_null($this->isSecure)){
    		$this->isSecure = 
    		(isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1)) ||
      		(isset($_SERVER['HTTP_SSL_HTTPS']) && (strtolower($_SERVER['HTTP_SSL_HTTPS']) == 'on' || $_SERVER['HTTP_SSL_HTTPS'] == 1))  ||
     		(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https');
    	}
    	return $this->isSecure;
	}
	
	public function setIsSecure($val){
		$this->isSecure = $val;
	}
	//
	public function isAjax()
	{
    	return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])  &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ||  isset($_GET['is_ajax']);
  	}
  	
  	public function getReferer($default = ''){
  		 return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $default;
  	}
  	
  	//
	public function getUrl()
	{
    	$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    	return $this->isAbsUrl($url) ? $url : $this->getUrlPrefix().$url;
    }
    
    //not perfect, it should be only used for additional parameters
    //Fixed: if we remove all ? & but not last one, & will be in url instead of ?
    public function createUrlWithout($url, $array)
    {
    	$tokens = array();
    	foreach ($array as $name)
    	{
    		$eName = urlencode($name);
    		if ( $eName != $name ) $name = "($eName|$name)";
    		$tokens[] = '#(^|/|\?|&)'.$name.'(=|/)[^/&?]*#';
    	}
   		$fragPos = strpos($url, '#');
    	if ( $fragPos !== false )
    	{
    		$url = substr( $url, 0, $fragPos-1 );
    	}
    	$val = preg_replace($tokens, '', $url);
    	//fix case where we have & and not ?
    	$strposAmp = strpos( $val, '&' );
    	if ( $strposAmp !== false )
    	{
    		if ( strpos($val, '?') === false )
    		{
    			$val[$strposAmp] = '?';
    		}
    	}
    	return $val;
    }
    
    public function getScriptName()
    {
    	if ($this->_scriptName) return $this->_scriptName;
    	$this->_scriptName = substr( $_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1 );
    	$this->_scriptName = preg_replace( '/[^A-Za-z0-9_\-.]/', '', $this->_scriptName );
    	return $this->_scriptName;
    }
    
    public function isModeRewriteActive()
    {
    	return $this->_isModeRewriteActive;
    }
 	 public function setIsModeRewriteActive($v)
    {
    	$this->_isModeRewriteActive = $v;
    }
    
    //set path url prefix - this is used on getPath func
    //@param string $prefix prefix it self
    public function setPathUrlPrefix($prefix)
    {
    	$this->_pathUrlPrefix = $prefix;
    	$len = strlen($prefix);
    	if ( $len && $prefix[$len-1] != '/' )
    	{
    		$this->_pathUrlPrefix .= '/';
    	}    	
    }
    /*
     * get Path/url without prefix and relative root
     */
    public function getPath()
    {
    	if ( !isset($_SERVER['REQUEST_URI']) ) return '';
    	$url = $_SERVER['REQUEST_URI'];
    	    	
    	if ( $this->isAbsUrl($url) )
    	{
    		$str = $this->getUrlPrefix().$this->getRelativeUrlRoot().$this->_pathUrlPrefix;
    	}
    	else 
    	{
    		$str = $this->getRelativeUrlRoot().$this->_pathUrlPrefix;
    	}
    	//remove prefix
    	$url = substr( $url, strlen($str) );
    	
    	/* //.htaccess part - uncomment this if you dont want to change .htaccess
    	$url = preg_replace( '#^/[^/]+\.php#', '', $url, -1, $cnt );
    	if ( $cnt )
    	{
    		$this->_isModeRewriteActive = true;
    	}
    	*/
    	
    	//remove fragment if exists
    	$fragPos = strpos($url, '#');
    	if ( $fragPos !== false )
    	{
    		$url = substr( $url, 0, $fragPos-1 );
    	}
    	//if url is empty return /
    	return $url ? $url : '/';
    }
    
    public function setUrlPrefix($val){
    	$this->urlPrefix = $val;	
    }
    
    public function getDynamicUrlPrefix($isSecure = false)
    {
    	if ( !$isSecure )
    	{
    		//if not secure and already defined - return it
    		if ( $this->normalUrlPrefix ) return $this->normalUrlPrefix;
    	}
    	//if its secure and already defined - return it
    	elseif ( $this->secureUrlPrefix )
    	{
    		return $this->secureUrlPrefix;
    	}
    	
    	if (!$isSecure)
    	{
    		$prefix = 'http://';
    		$standardPort = '80';	
    	}
    	else
    	{
   			$prefix = 'https://';
   			$standardPort = '443';
   		}
   	    $hostParam = explode(':', $_SERVER['HTTP_HOST']);
   	    if (count($hostParam) == 1) 
   		{
      		$hostParam[] = isset($_SERVER['SERVER_PORT']) ? 
      					   $_SERVER['SERVER_PORT'] : $standardPort;
    	}
   		if ($hostParam[1] == $standardPort)
    	{
      		unset($hostParam[1]);
    	}
    	
    	if ( !$isSecure )
    	{
    		$this->normalUrlPrefix = $prefix.join( ':',$hostParam );
    		return $this->normalUrlPrefix;
    	}
    	
    	$this->secureUrlPrefix = $prefix.join( ':',$hostParam );
    	return $this->secureUrlPrefix;
    	
    }
    
    public function getUrlPrefix()
    {
    	if ( is_null($this->urlPrefix) )
    	{
    		$this->urlPrefix = $this->getDynamicUrlPrefix( $this->isSecure() );
    	}
    	return $this->urlPrefix;
    }
    
    
    public function getHost()
    {
		return $_SERVER['HTTP_HOST'];
    }
	
	public function getCookie($name, $defaultValue = null)
	{
		if ( isset($_COOKIE[$name]) )
		{
			return !$this->magicQuotes ? $_COOKIE[$name] : cUtil::stripslashes($_COOKIE[$name]);
		}
		return $defaultValue;
	}


	/**
 	  * Returns the content type of the current request.
	   *
	   * @param  Boolean $trimmed If false the full Content-Type header will be returned
	   *
	   * @return string
	   */
  	public function getContentType($trim = true)
  	{
   		$contentType = $this->getHttpHeader('Content-Type', null);
	
  		if ( $trim && false !== $pos = strpos($contentType, ';') )
	    {
	        return substr($contentType, 0, $pos);
	    }
	
	    return $contentType;
 	 }
  
	/**
   * See if the client is using absolute uri
   *
   * @return boolean true, if is absolute uri otherwise false
   	*/
 	public function isAbsUrl($url)
  	{
	  	return strpos( $url, 'http') === 0;
 	 }
 	 
 	 

  public function getHttpHeader($name, $prefix = 'HTTP')
  {
	    $name = $prefix.'_'.strtoupper($name);
    	return isset($_SERVER[$name]) ?
    		 icUtil::stripslashes( $_SERVER[$name] ) : NULL;
  }

 	 
}
