<?php
class GR_Router
{	

	/**
	 * 参数数组
	 *
	 * @var Array
	 */
	protected $params = array();
	
	/**
	 * 路由规则
	 *
	 * @var Array
	 */
	protected $_routers = array();
	
	/**
	 * 对象实例
	 *
	 * @var Object
	 */
	private static $_instance = NULL;
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		// 加载框架路由配置文件
		$file_path = APP_PATH.'/config/router.php';
		if ( ! file_exists($file_path)) {
			exit('The router configuration file does not exist.');
		}
		
		require($file_path);
		
		if ( ! isset($router) || ! is_array($router)) {
			exit('Your  router config file does not appear to be formatted correctly.');
		}
		
		$this->_routers = $router;
		unset($router);
		
		$this->init();
		
		self::$_instance =& $this;
	}
	
	/**
	 * 获取对象实例
	 * 
	 * @return object
	 */
	public static function &getInstance()
	{
		return self::$_instance;
	}
	
	/**
	 * 初始化路由
	 */
	public function init()
	{
		$delimiter = $this->_routers['delimiter'];
		$postfix   = $this->_routers['postfix'];
		
		if (CLI === FALSE) {
			$type = strtoupper($this->_routers['type']);
			if ('STANDARD' == $type) {
				// TDDO &
				$this->params =& $_GET;
				return TRUE;
			}
			
			$uri = '';
			// 获取PATH_INFO
			if (isset($_SERVER['PATH_INFO'])) {
				$uri = $_SERVER['PATH_INFO'];
			} else {
				if (isset($_SERVER['PHP_SELF']) && isset($_SERVER['SCRIPT_NAME'])) {
					$uri = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']);
				}
			}
			
			$uri = rtrim($uri, '/');
			if ($uri != '') {
				// 忽略后缀
				$postfix && $uri = rtrim($uri, $postfix);
				$uri = explode('/', ltrim($uri, '/'));
				$this->matchRoute($uri);
			} else {
				!empty($_GET) && $this->params =& $_GET;
			}
		} else { 
			// CLI
			$i = 0; 
			$module = ''; 
			$contrl = ''; 
			$method = '';
			while ((empty($module) || empty($contrl) || empty($method)) && isset($_SERVER['argv'][$i])) {
				switch ($_SERVER['argv'][$i]) {
					case '-m':
					case '--module':
						$module = (isset($_SERVER['argv'][$i+1]) ? $_SERVER['argv'][$i+1] : '');
					break;
					
					case '-c':
					case '--contrl':
						$contrl = (isset($_SERVER['argv'][$i+1]) ? $_SERVER['argv'][$i+1] : '');
					break;
					
					case '-f':
					case '--func':
						$method = (isset($_SERVER['argv'][$i+1]) ? $_SERVER['argv'][$i+1] : '');
					break;
				}
				$i++;
			}
			$this->params['module'] = $module;
			$this->params['contrl'] = $contrl;
			$this->params['method'] = $method;
		}
		
		$this->params['module'] = (isset($this->params['module']) ? $this->params['module'] : 'home');
		$this->params['contrl'] = (isset($this->params['contrl']) ? $this->params['contrl'] : 'index');
		$this->params['method'] = (isset($this->params['method']) ? $this->params['method'] : 'main');
	}
	
	/**
	 * 匹配URL地址
	 * 
	 * @param string $url
	 */
	public function matchRoute($url)
	{
		$ret  = array();
		$reqs      = $this->_routers['reqs'];
		$delimiter = $this->_routers['delimiter'];
		$varprefix = $this->_routers['varprefix'];
		$postfix   = $this->_routers['postfix'];
		$pattern   = explode('/', trim(trim($this->_routers['pattern'], '/')));
		
		// 预处理URL
		if (is_string($url)) {
			$url = rtrim($url, $postfix); //忽略后缀
			$url = explode('/', trim($url, $delimiter));
		}
		
		foreach($pattern as $k => $v) {
			if ($v[0] == $varprefix) {
				$varname = substr($v, 1);
				if (isset($url[$k]) && isset($this->_routers['reqs'][$varname])) {
					$regex = "/^{$this->_routers['reqs'][$varname]}\$/i";
					if (preg_match($regex, $url[$k])) {
						$ret[$varname] = $url[$k];
					}
				}
			} else if ($v[0] == '*') {
				if ('/' != $delimiter) {
					$param = array_pop($url);
					$url = array_merge($url, explode($delimiter, $param));
				}
				switch (strtoupper($this->_routers['type'])) {
					case 'PATH_PARM':
						$ret['_param'] = (isset($url[$k]) ? array_slice($url, $k) : array());
					break;
					
					case 'PATH_INFO':
						$pos = $k;
						while (isset($url[$pos]) && isset($url[$pos + 1])) {
							$ret[$url[$pos++]] = urldecode($url[$pos]);
							$pos++;
						}
					break;
				};
			} else {
				// 伪静态
			}
		}
		$this->params = $ret;
		return TRUE;
	}
	
	/**
	 * 生成URL地址
	 * 
	 * @param array $params
	 */
	public function reverseMatchRoute($params)
	{
		$url = $params;
		$type = strtoupper($this->_routers['type']);
		if ('STANDARD' == $type) {
			$tmp = '?';
			foreach($url as $key => $value) {
				$tmp .= $key . '=' . rawurlencode($value) . '&';
			}
			return $_SERVER['SCRIPT_NAME'] . rtrim($tmp, '&');
		}
		
		$ret = $this->_routers['pattern'];
		$default   = $this->_routers['default'];
		$reqs      = $this->_routers['reqs'];
		$delimiter = $this->_routers['delimiter'];
		$varprefix = $this->_routers['varprefix'];
		$postfix   = $this->_routers['postfix'];
		
		$pattern = explode('/', trim($this->_routers['pattern'], '/'));
		
		foreach($pattern as $k => $v) {
			if ($v[0] == $varprefix) {
				$varname = substr($v, 1);
				if (isset($url[$varname]) && $url[$varname] != '' && isset($this->_routers['reqs'][$varname])) {
					$regex = "/^{$this->_routers['reqs'][$varname]}\$/i";
					if (preg_match($regex, $url[$varname])) {
						$ret = str_replace($v, $url[$varname], $ret);
						unset($url[$varname]);
					}
				} else if (isset($default[$varname])) {
					$ret = str_replace($v, $default[$varname], $ret);
				}
			} else if ($v[0] == '*') {
				$tmp = '';
				if ('PATH_PARM' == $type) {
					$tmp = implode($delimiter, $url);
				}
				if ('PATH_INFO' == $type) {
					foreach($url as $key => $value) {
						$tmp .= $key . $delimiter . rawurlencode($value) . $delimiter;
					}
				}
				$tmp = rtrim($tmp, $delimiter);
				$ret = str_replace($v, $tmp, $ret);
				$ret = rtrim($ret, $delimiter);
			} else { 
				// 静态
			}
		}
		if ('PATH_INFO' == $type || 'PATH_PARM' == $type) {
			$ret = $_SERVER['SCRIPT_NAME'] . '/' . $ret . $postfix;
		} else {
			$ret = $ret . $postfix;
		}
		return $ret;
	}
	
	public function site_url($uri, $args = array())
	{
		$data = explode('/', $uri);
		$tmp  = array();
		$tmp['module'] = (isset($data[0]) && $data[0] != '') ? $data[0] : 'home';
		$tmp['contrl'] = (isset($data[1]) && $data[1] != '') ? $data[1] : 'index';
		$tmp['method'] = (isset($data[2]) && $data[2] != '') ? $data[2] : 'main';
		$args = array_merge($tmp, $args);
		return $this->reverseMatchRoute($args);
	}
	
	/**
	 * 获取模块
	 */
	public static function getModule()
	{
		$router = self::getInstance();
		return (isset($router->params['module']) ? $router->params['module'] : $router->_routers['default']['module']);
	}
	
	/**
	 * 获取控制器
	 */
	public static function getContrl()
	{
		$router = self::getInstance();
		return (isset($router->params['contrl']) ? $router->params['contrl'] : $router->_routers['default']['contrl']);
	}
	
	/**
	 * 获取方法
	 */
	public static function getMethod()
	{
		$router = self::getInstance();
		return (isset($router->params['method']) ? $router->params['method'] : $router->_routers['default']['method']);
	}
	
	/**
	 * 获取方法参数
	 * 
	 * @param  int   $index 键值索引
	 * @return mixed 参数值
	 */
	public static function getArgv($index = NULL)
	{
		$router = self::getInstance();
		// TODO 不是PATH_PARM模式
		if ( ! isset($router->params['_param'])) {
			return FALSE;
		}
		
		if ($index !== NULL) {
			return (isset($router->params['_param'][$index]) ? $router->params['_param'][$index] : FALSE);
		}
		return $router->params['_param'];
	}
	
	public static function getPathInfo()
	{
		 return ( self::getVar('PATH_INFO', 'v') ? self::getVar('PATH_INFO', 'v') : '/'.DEFAULT_MODULE );
	}
	
	public static function getUrl()
	{
		return self::getBaseURL().self::getURI();
	}
	
	public static function getBaseUrl()
	{
		$self   = self::getVar('PHP_SELF', 'v');
        $https  = self::getVar('HTTPS', 'v');
        $server = self::getVar('HTTP_HOST', 'v');
		
        $server .= rtrim(str_replace(strstr($self, 'index.php'), '', $self), '/');
		$pre = ( $https == 'on' ? 'https://' : 'http://' );
        return $pre.$server;
	}
	
	public static function redirect($url = '/', $status = NULL)
	{
		$url = str_replace(array('\r','\n','%0d','%0a'), '', $url);
		
		if (headers_sent()) {
            return FALSE;
        }
    
        // 跳转前停止SESSION写入
        session_write_close();
    
        if (is_null($status)){
            $status = '302';
        }
		
		// 发送状态到浏览器中
        if ((int)$status > 0) {
            switch($status){
                case '301': $msg = '301 Moved Permanently'; break;
                case '307': $msg = '307 Temporary Redirect'; break;
                case '401': $msg = '401 Access Denied'; break;
                case '403': $msg = '403 Request Forbidden'; break;
                case '404': $msg = '404 Not Found'; break;
                case '405': $msg = '405 Method Not Allowed'; break;
                case '302':
                default: $msg = '302 Found'; break; // temp redirect
            }
            if (isset($msg)) {
                header('HTTP/1.1 '.$msg);
            }
        }
        if (preg_match('/^https?/', $url)) {
            header("Location: $url");
            exit();
        }
        // strip leading slashies
        $url = preg_replace('!^/*!', '', $url);
        header("Location: ".self::getBaseURL().'/'.$url);
        exit();
	}
	
	public static function getVar($key, $mode, $default = NULL)
	{
		$data = array();
		switch(strtolower($mode)) {
			case 'g': $data =& $_GET; break;
			case 'p': $data =& $_POST; break;
			case 'c': $data =& $_COOKIE; break;
			case 'f': $data =& $_FILES; break;
			case 'b': $data =& $GLOBALS; break;
			case 'r': $data =& $_REQUEST; break;
			case 's': $data =& $_SESSION; break;
			case 'v': $data =& $_SERVER; break;
		}
		if (isset($data[$key])) {
			return $data[$key];
		}
		return $default;
	}
	
}
?>