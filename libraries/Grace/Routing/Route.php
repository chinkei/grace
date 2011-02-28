<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 路由类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Routing
 */
class Grace_Routing_Route
{
	/**
     * 
     * The $path property converted to a regular expression, using the $params
     * subpatterns.
     * 
     * @var string
     * 
     */
    protected $_regex;

    /**
     * 
     * All param matches found in the path during the `isMatch()` process.
     * 
     * @var string
     * 
     * @see isMatch()
     * 
     */
    protected $_matches;
		
	/**
     * 路由名称
     * 
     * @var string
     */
	protected $_name;
	
	/**
	 * 找到对应的路由参数映射的正则规则
	 * 
	 * @var array
	 */
	protected $_pattern;
	
	/**
	 * 路由正则匹配的后的参数数组
	 * 
	 * @var array
	 */
	protected $_parameters = array();
	
	/**
	 * 要匹配路由的地址
	 *
	 * @var string
	 */
	protected $_path;
	
	/**
	 *
	 */
	protected $_method;
	
	/**
     * 
     * When true, the `HTTPS` value must be `on`, or the `SERVER_PORT` must be
     * 443.  When false, neither of those values may be present.  When null, 
     * it is ignored.
     * 
     * @var bool
     * 
     */
    protected $_secure = NULL;

	/**
     * 
     * If routable, this route should be used in matching.  If not, it should
     * be used only to generate a path.
     * 
     * @var bool
     * 
     */
    protected $_routable;
	
	/**
     * 
     * A callable to provide custom matching logic against the 
     * server values and matched params from this Route. The signature must be 
     * `function(array $server, \ArrayObject $matches)` and must return a 
     * boolean: true to accept this Route match, or false to deny the match. 
     * Note that this allows a wide range of manipulations, and further allows 
     * the developer to modify the matched params as needed.
     * 
     * @var callable
     * 
     * @see isMatch()
     * 
     */
    protected $_is_match;
	
	/**
     * 
     * A callable to modify path-generation values. The signature 
     * must be `function($route, array $data)`; its return value is an array 
     * of data to be used in the path. The `$route` is this Route object, and 
     * `$data` is the set of key-value pairs to be interpolated into the path
     * as provided by the caller.
     * 
     * @var callable
     * 
     * @see generate()
     * 
     */
    protected $_generate;

    /**
     * 
     * A prefix for the Route name, generally from attached route groups.
     * 
     * @var string
     * 
     */
    protected $_name_prefix;

    /**
     * 
     * A prefix for the Route path, generally from attached route groups.
     * 
     * @var string
     * 
     */
    protected $_path_prefix;
	
	/**
     * 
     * Retain debugging information about why the route did not match.
     * 
     * @var array
     * 
     */
    protected $_debug;
	
	protected $_default_params = array(
		'name'        => NULL,
		'path'        => NULL,
		'pattern' => NULL,
		'parameters'  => NULL,
		'method'      => NULL,
		'secure'      => NULL,
		'routable'    => TRUE,
		'is_match'    => NULL,
		'generate'    => NULL,
		'name_prefix' => NULL,
		'path_prefix' => NULL
	);
	
	/**
	 * 构造函数
	 * 
	 * @param array $params 参数数组
	 */
	public function __construct($params = array())
	{	
		$params = array_merge($this->_default_params, $params);
		
		// 初始化路由名称
        if ( $params['name_prefix'] && $$params['name'] ) {
            $this->_name = $params['name_prefix'] . $$params['name'];
			unset($params['name']);
        }
		
		// 初始化路由路径
        if ( $params['path_prefix'] && $params['path'] && ( strpos($params['path'], '://') === FALSE ) ) {
            $this->_path = $params['path_prefix'] . $params['path'];
            $this->_path = str_replace('//', '/', $this->_path);
			unset($params['path']);
        }
		
		$keys = array_keys($this->_default_params);
		// 初始化其他参数
		foreach ($keys as $key) {
			$_key = '_'.$key;
			$this->$_key = $params[$key];
		}
        // 设置路由为一个正则表达式
        $this->_setRegex();
    }
	
	/**
     * 
     * 获取保护的属性值
     * 
     * @param string $key 属性键值
     * @return mixed
     * 
     */
    public function __get($key)
    {
		$key = '_'.$key;
        return isset($this->$key) ? $this->$key : NULL;
    }
	
	/**
	 * 根据参数数组生成路径
	 * 
	 * @param  array  $data 参数值
	 * @return string 路径地址
	 */
	public function generate($data = array())
    {
        // 是否存在预处理数据的callable
        if ($this->_generate) {
            $generate = $this->_generate;
            $data = $generate($this, $data);
        }

        // 根据参数生成路径
        $search  = array();
        $replace = array();
        $data = array_merge($this->_parameters, $data);
        foreach ($data as $key => $val) {
			if ($key == '*') {
				$search[]  = "?{:__wildcard__}";
				if (is_array($val)) {
					$val = implode('/', array_map('urlencode', $val));
				} else {
					$val = urlencode($val);
				}
				$replace[] = $val;
			} else {
				$search[]  = "{:$key}";
				$replace[] = urlencode($val);
			}
        }
        return str_replace($search, $replace, $this->_path);
    }
	
	public function isMatch($path, $server = array())
	{
		if (! $this->_routable) {
            $this->_debug[] = 'Not routable.';
            return FALSE;
        }

        $is_match = $this->_isRegexMatch($path)
                 && $this->_isMethodMatch($server)
                 && $this->_isSecureMatch($server)
                 && $this->_isCustomMatch($server);

        if (! $is_match) {
            return FALSE;
        }

        // populate the path matches into the route values
        foreach ($this->_matches as $key => $val) {
            if (is_string($key)) {
                $this->_parameters[$key] = $val;
            }
        }

        // populate wildcard matches
        if (isset($this->_pattern['__wildcard__'])) {
            $values = $this->_parameters['__wildcard__'];
            unset($this->_parameters['__wildcard__']);
            if ($values) {
                $this->_parameters['*'] = explode('/', $values);
            } else {
                $this->_parameters['*'] = array();
            }
        }
        
        // done!
        return TRUE;
	}
	
	/**
     * 设置基于参数的正则表达式
     * 
     * @return void
     */
    protected function _setRegex()
    {
        if (substr($this->_path, -2) == '/*') {
            // 路径结尾如果是个通配符/*的话, 给个特殊的参数
            $this->_path = substr($this->_path, 0, -2) . "/?{:__wildcard__:(.*)}";
        }
        
        // 提取参数和对应的正则表达式
        $find = "/\{:(.*?)(:(.*?))?\}/";
        preg_match_all($find, $this->_path, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $whole = $match[0];
            $name  = $match[1];
			// 是否存在正则规则
            if (isset($match[3])) {
                $this->_pattern[$name] = $match[3];
                $this->_path = str_replace($whole, "{:$name}", $this->_path);
            } else {
				// 不存在的规则项则设置默认规则
				if ( ! isset($this->_pattern[$name]) ) {
					$this->_pattern[$name] = "([^/]+)";
				}
            }
        }

        // 创建正则表达式模式的参数和规则
        $this->_regex = $this->_path;
        if ($this->_pattern) {
            $search  = array();
            $replace = array();
            foreach ($this->_pattern as $name => $subpattern) {
                if ($subpattern[0] != '(') {
                    $message = "Subpattern for param '$name' must start with '('.";
                    throw new Exception($message);
                } else {
                    $search[]  = "{:$name}";
                    $replace[] = "(?P<$name>" . substr($subpattern, 1);
                }
            }
            $this->_regex = str_replace($search, $replace, $this->_regex);
        }
    }
	
	/**
	 * 正则匹配
	 * 
	 * @param  string $path 路径地址
	 * @return bool   是否匹配成功
	 */
	protected function _isRegexMatch($path)
	{
		$regex  = "#^{$this->_regex}$#";
		$retFlg = preg_match($regex, $path, $this->_matches);
		if (! $retFlg) {
            $this->_debug[] = 'Not a regex match.';
        }
        return $retFlg;
	}
	
	/**
	 * request method 匹配
	 * 
	 * @param  string $path 路径地址
	 * @return bool   是否匹配成功
	 */
	protected function _isMethodMatch($server)
	{
		if (isset($this->_method)) {
            if (! isset($server['REQUEST_METHOD'])) {
                $this->_debug[] = 'Method match requested but REQUEST_METHOD not set.';
                return FALSE;
            }
            if (! in_array($server['REQUEST_METHOD'], $this->_method)) {
                $this->_debug[] = 'Not a method match.';
                return FALSE;
            }
        }
        return TRUE;
	}
	
	protected function _isSecureMatch()
	{
		 if ($this->_secure !== NULL) {

            $is_secure = (isset($server['HTTPS']) && $server['HTTPS'] == 'on')
                      || (isset($server['SERVER_PORT']) && $server['SERVER_PORT'] == 443);

            if ($this->_secure == TRUE && ! $is_secure) {
                $this->_debug[] = 'Secure required, but not secure.';
                return FALSE;
            }

            if ($this->_secure == FALSE && $is_secure) {
                $this->_debug[] = 'Non-secure required, but is secure.';
                return FALSE;
            }
        }
        return TRUE;
	}
	
	/**
	 * 自定义匹配
	 * 
	 * @return bool   是否匹配成功
	 */
	protected function _isCustomMatch()
	{
		 if ( !$this->_is_match ) {
            return TRUE;
        }

        // pass the matches as an object, not as an array, so we can avoid
        // tricky hacks for references
        $matches  = new \ArrayObject($this->_matches);
        $is_match = $this->_is_match;
        $retFlg   = $is_match($server, $matches);

        // convert back to array
        $this->_matches = $matches->getArrayCopy();

        // did it match?
        if (! $result) {
            $this->_debug[] = 'Not a custom match.';
        }

        return $retFlg;
	}
}
?>