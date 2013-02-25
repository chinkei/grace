<?php
interface Grace_Network_Request_Interface
{
	/**
	 * 获取对象唯一实例
	 *
	 * @param string $configFile 配置文件路径
	 * @return object 返回本对象实例
	 */
	public static function getInstance($url = '');
	
	/**
	 * 设置需要发送的HTTP头信息
	 * 
	 * @param array/string 需要设置的头信息，可以是一个 类似 array('Host: example.com', 'Accept-Language: zh-cn') 的头信息数组
	 * 						或单一的一条类似于 'Host: example.com' 头信息字符串
	 * @return void
	 */
	public function setHeader($header);
	
	/**
	 * 设置Cookie头信息
	 * 
	 * 注意：本函数只能调用一次，下次调用会覆盖上一次的设置
	 *
	 * @param string/array 需要设置的Cookie信息，一个类似于 'name1=value1&name2=value2' 的Cookie字符串信息，
	 * 					   或者是一个 array('name1'=>'value1', 'name2'=>'value2') 的一维数组
	 * @return void
	 */
	public function setCookie($cookie);
	
	/**
	 * 设置要发送的数据信息
	 *
	 * 注意：本函数只能调用一次，下次调用会覆盖上一次的设置
	 *
	 * @param array 设置需要发送的数据信息，一个类似于 array('name1'=>'value1', 'name2'=>'value2') 的一维数组
	 * @return void
	 */
	public function setVar($vars);
	
	/**
	 * 设置要请求的URL地址
	 *
	 * @param string $url 需要设置的URL地址
	 * @return void
	 */
	public function setUrl($url);
	

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
	public function get($url = '', $vars = array(), $header = array(), $cookie = '', $timeout = 5, $options = array());
	

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
	public function post($url = '', $vars = array(), $header = array(), $cookie = '', $timeout = 5, $options = array());	
	
	/**
	 * 发送HTTP请求核心函数
	 *
	 * @param string $method 使用GET还是POST方式访问
	 * @param array $vars 需要另外附加发送的GET/POST数据
	 * @param int $timeout 连接对方服务器访问超时时间，单位为秒
	 * @param array $options 当前操作类一些特殊的属性设置
	 * @return string 返回服务器端读取的返回数据
	 */
	public function send($method = 'GET', $timeout = 5, $options = array());
}
?>