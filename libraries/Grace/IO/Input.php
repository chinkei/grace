<?php
uses('Grace_Filter_Filter');

class Grace_IO_Input
{
    /**
	 * 构造函数
	 */
	public function __construct()
	{
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