<?php
abstract class Grace_Network_Request
{
	/**
	 * @var string 需要发送的cookie信息
	 */
	protected $_strCookies = '';
	/**
	 * @var array 需要发送的头信息
	 */
	protected $_headers = array();
	/**
	 * @var string 需要访问的URL地址
	 */	
	protected $_uri	= '';
	/**
	 * @var array 需要发送的数据
	 */
	protected $_params = array();
	
	public static function loadDriver($parms)
	{
		
	}
	
	protected function _buildQuery($data, $sep = '&'){
		$encoded = '';
		while (list($k,$v) = each($data)) { 
			$encoded .= ($encoded ? "$sep" : "");
			$encoded .= rawurlencode($k)."=".rawurlencode($v); 
		} 
		return $encoded;		
	}
	
	/**
	 * 设置需要发送的HTTP头信息
	 * 
	 * @param array/string 需要设置的头信息，可以是一个 类似 array('Host: example.com', 'Accept-Language: zh-cn') 的头信息数组
	 * 						或单一的一条类似于 'Host: example.com' 头信息字符串
	 * @return void
	 */
	public function setHeader($data)
	{
		if (empty($data)) {
			return FALSE;
		}
		
		if (is_array($data)) {
			foreach ($data as $k => $v){
				$this->_headers[] = is_numeric($k) ? trim($v) : (trim($k) .": ". trim($v));				
			}
		} else {
			if (is_string($data)) {
				$this->_headers[] = $data;
			}
		}
		return TRUE;
	}
	
	/**
	 * 设置Cookie头信息
	 * 
	 * 注意：本函数只能调用一次，下次调用会覆盖上一次的设置
	 *
	 * @param string/array 需要设置的Cookie信息，一个类似于 'name1=value1&name2=value2' 的Cookie字符串信息，
	 * 					   或者是一个 array('name1'=>'value1', 'name2'=>'value2') 的一维数组
	 * @return void
	 */
	public function setCookie($data)
	{
		if (empty($data)) {
			return FALSE;
		}
		
		if (is_array($data)) {
			$this->_strCookies = $this->_buildQuery($data, ';');
		} else {
			if (is_string($data)) {
				$this->_strCookies = $data;
			}
		}
		return TRUE;
	}
	
	/**
	 * 设置要发送的数据信息
	 *
	 * 注意：本函数只能调用一次，下次调用会覆盖上一次的设置
	 *
	 * @param array 设置需要发送的数据信息，一个类似于 array('name1'=>'value1', 'name2'=>'value2') 的一维数组
	 * @return void
	 */
	public function setParams($data){
		if ( empty($data) ) {
			return FALSE;
		}
		
		if (is_array($data)){
			$this->_params = $data;
		}
		return TRUE; 
	}
	
	/**
	 * 设置要请求的URL地址
	 *
	 * @param string $url 需要设置的URL地址
	 * @return void
	 */
	public function setUrl($url){
		if ($url != '') {
			$this->_uri = $url;
			return TRUE;
		}
		return FALSE;
	}
	

	/**
	 * 发送HTTP GET请求
	 *
	 * @param string $url 如果初始化对象的时候没有设置或者要设置不同的访问URL，可以传本参数
	 * @param array $vars 需要单独返送的GET变量
	 * @param array/string 需要设置的头信息，可以是一个 类似 array('Host: example.com', 'Accept-Language: zh-cn') 的头信息数组
	 * 					   或单一的一条类似于 'Host: example.com' 头信息字符串
	 * @param string/array 需要设置的Cookie信息，一个类似于 'name1=value1&name2=value2' 的Cookie字符串信息，
	 * 					   或者是一个 array('name1'=>'value1', 'name2'=>'value2') 的一维数组
	 * @param int $timeout 连接对方服务器访问超时时间，单位为秒
	 * @param array $options 当前操作类一些特殊的属性设置
	 * @return unknown
	 */
	public function get($url = '', $params  = array(), $header = array(), $cookie = '', $timeout = 5, $options = array()){
		$this->setUrl($url);
		$this->setHeader($header);
		$this->setCookie($cookie);
		$this->setParams($params);
		return $this->send('GET', $timeout);
	}	
	

	/**
	 * 发送HTTP POST请求
	 *
	 * @param string $url 如果初始化对象的时候没有设置或者要设置不同的访问URL，可以传本参数
	 * @param array $vars 需要单独返送的GET变量
	 * @param array/string 需要设置的头信息，可以是一个 类似 array('Host: example.com', 'Accept-Language: zh-cn') 的头信息数组
	 * 					   或单一的一条类似于 'Host: example.com' 头信息字符串
	 * @param string/array 需要设置的Cookie信息，一个类似于 'name1=value1&name2=value2' 的Cookie字符串信息，
	 * 					   或者是一个 array('name1'=>'value1', 'name2'=>'value2') 的一维数组
	 * @param int $timeout 连接对方服务器访问超时时间，单位为秒
	 * @param array $options 当前操作类一些特殊的属性设置
	 * @return unknown
	 */
	public function post($url = '', $params = array(), $header = array(), $cookie = '', $timeout = 5, $options = array()){
		$this->setUrl($url);
		$this->setHeader($header);
		$this->setCookie($cookie);
		$this->setParams($params );
		return $this->send('POST', $timeout);
	}	
}
?>