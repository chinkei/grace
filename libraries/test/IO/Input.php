<?php
//uses('Grace_Filter_Filter');

class Grace_Filter_Filter
{
	/**
	 * 过滤全部用户输入字符
	 * 
	 * @return void
	 */
	public static function init() 
	{
		$request_method = array ('_GET', '_POST', '_COOKIE');
		foreach ($request_method as $k => $request)
		{
			foreach ($GLOBALS[$request] as $_k => & $_v)
			{
				$_v = self::runMagicQuote($_v);
			}
		}
	}
	
	/**
	  * 剔除JavaScript、CSS、Object、Iframe
	  */
	 public static function removeScript($text){
		$text = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","&111n\\2",$text);
		$text = preg_replace ("/<style.+<\/style>/iesU", '', $text);
		$text = preg_replace ("/<script.+<\/script>/iesU", '', $text);
		$text = preg_replace ("/<iframe.+<\/iframe>/iesU", '', $text);
		$text = preg_replace ("/<object.+<\/object>/iesU", '', $text);
		return $text;
	 }

	
	/**
	 * 特殊字符转义
	 * 
	 * @param mixed $vars
	 */
	public static function runMagicQuote(& $vars) 
	{
	    if(empty($vars))
	    {
	        return $vars;    
	    }
	    else if (is_array($vars)) 
		{
			foreach ($vars as $_k => $_v) 
			{
				$vars[$_k] = self::runMagicQuote($_v);
			}
		} 
		else 
		{
			$vars = self::strReplace($vars);
		}
		return $vars;
	}
	

	/**
	 * 功能:特殊字符替换
	 * 
	 * @param string $val 要过滤的字符串
	 * @return string 返回过滤后的字符串
	 */
	public static function strReplace($val) 
	{
	    $val = Security::xssClean($val);
		$val = ( !get_magic_quotes_gpc() ) ? addcslashes($val, "\000\n\r\\'\"\032") : $val;
        $val = htmlspecialchars($val);
		return $val;
	}
	

    /**
	 * 
	 * XSS攻击过滤
	 * 
	 * @param mixed $val 需要过滤的值
	 * @return mixed
	 */
    public static function removeXSS($val) {
        
        if (empty($val)) return '';
	    
	    if (is_array($val)) {
			while (list($key) = each($val)) {
				$val[$key] = self::removeXSS($val[$key]);
			}
			return $val;
		}
        
	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	   // this prevents some character re-spacing such as <java\0script>
	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
	
	   // straight replacements, the user should never need these since they're normal characters
	   // this prevents like <IMG SRC=@avascript:alert('XSS')>
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
		  // ;? matches the ;, which is optional
		  // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
	
		  // @ @ search for the hex values
		  $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
		  // @ @ 0{0,7} matches '0' zero to seven times
		  $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	   }
	
	   // now the only remaining whitespace attacks are \t, \n, and \r
	   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	   $ra = array_merge($ra1, $ra2);
	
	   $found = true; // keep replacing as long as the previous round replaced something
	   while ($found == true) {
		  $val_before = $val;
		  for ($i = 0; $i < sizeof($ra); $i++) {
			 $pattern = '/';
			 for ($j = 0; $j < strlen($ra[$i]); $j++) {
				if ($j > 0) {
				   $pattern .= '(';
				   $pattern .= '(&#[xX]0{0,8}([9ab]);)';
				   $pattern .= '|';
				   $pattern .= '|(&#0{0,8}([9|10|13]);)';
				   $pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			 }
			 $pattern .= '/i';
			 $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
			 $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
			 if ($val_before == $val) {
				// no replacements were made, so exit the loop
				$found = false;
			 }
		  }
	   }
	   return $val;
	}
}

class Grace_IO_Input
{
	/**
	 * @var object 对象单例
	 */
	static $_instance = NULL;

	/**
	 * 保证对象不被clone
	 */
	private function __clone() {}

    /**
	 * 构造函数
	 */
	private function __construct() {}


	/**
	 * 获取对象唯一实例
	 *
	 * @param  void
	 * @return object 返回本对象实例
	 */
	public static function getInstance()
	{
		if (!(self::$_instance instanceof self)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 获取GET传递过来的参数
	 *
	 * @param  string $key   需要获取的Key名(空则返回所有数据)
	 * @param  bool   $isXss 是否进行Xss过滤
	 * @return mixed
	 */
	public function get($key = '', $isXss = FALSE)
	{
		return $this->_getData($key, $_GET, $isXss);
	}

	/**
	 * 获取POST传递过来的参数
	 *
	 * @param string $key 需要获取的Key名，空则返回所有数据
	 * @return mixed
	 */
	public function post($key = '', $isXss = FALSE)
	{
		return $this->_getData($key, $_POST, $isXss);
	}
	
	public function cookie($key = '', $isXss = FALSE)
	{
		return $this->_getData($key, $_COOKIE, $isXss);
	}
	
	/**
	 * 获取REQUEST传递过来的参数
	 *
	 * @param string $key 需要获取的Key名，空则返回所有数据
	 * @return mixed
	 */
	public function request($key = '', $isXss = FALSE)
	{
		return $this->_getData($key, $_REQUEST, $isXss);
	}
	
	/**
	 * 获取REQUEST传递过来的参数
	 *
	 * @param string $key 需要获取的Key名，空则返回所有数据
	 * @return mixed
	 */
	public function server($key = '')
	{
		return $this->_getData($key, $_SERVER, FALSE);
	}
	
	/**
	 * 获取REQUEST传递过来的参数
	 *
	 * @param string $key 需要获取的Key名，空则返回所有数据
	 * @return mixed
	 */
	public function env($key = '')
	{
		return $this->_getData($key, $_ENV, FALSE);
	}
	
	public function file($key = '')
	{
		return $this->_getData($key, $_FILES, FALSE);
	}

	/**
	 * 获取GET/POST传递过来的参数
	 *
	 * @param string $key 需要获取的Key名，空则返回所有数据
	 * @return mixed
	 */
	public function gp($key, $isXss)
	{
		if (isset($_GET[$key])) {
			return $this->get($key);
		}
		if (isset($_POST[$key])) {
			return $this->post($key);
		}
		return FALSE;
	}
	
	/**
	 * 是否是一个GET请求
	 *
	 * @param void
	 * @return bool
	 */
	public function isGet()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'GET'){
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * 是否是一个ajax请求
	 *
	 * @param void
	 * @return bool
	 */
	public function isAjax()
	{
		if( $this->get('is_ajax') != '' OR (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * 是否是一个POST请求
	 *
	 * @param void
	 * @return bool
	 */
	public function isPost()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST'){
			return FALSE;
		}
		return TRUE;	
	}
	
	/**
	 * 获取当前客户端IP地址
	 *
	 * @return string 当前访问的客户端IP
	 */
	public function getClientIp()
	{
		$ip = 'Unknow';
	 	if ( !empty($_SERVER['HTTP_CLIENT_IP']) ) { 
	        $ip = $_SERVER['HTTP_CLIENT_IP']; 
	    } elseif ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) { 
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	    } else { 
	        $ip = $_SERVER['REMOTE_ADDR']; 
	    } 
	    return $ip;
	}
	
	/**
	 * 获取数组中某个元素的值
	 * 
	 * @param  string $needle 元素
	 * @param  array  $array  数组
	 * @return mixed 
	 */
	protected function _getData($key = '', &$array = array(), $isXss = FALSE)
	{
		$data = '';
		if ($key != '') {
			if ( ! isset($array[$key]) ) {
				return FALSE;
			}
			$data = $array[$key];
		} else {
			$data = $array;
		}
		
		return $isXss === FALSE ? $data : Grace_Filter_Filter::removeXSS($data);
	}
}
?>