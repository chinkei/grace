<?php
class Grace_Network_Request
{
	/**
	 * @var 使用 CURL
	 */
	const TYPE_CURL			= 1;
	/**
	 * @var 使用 Socket
	 */	
	const TYPE_SOCK			= 2;
	/**
	 * @var 使用 Stream
	 */	
	const TYPE_STREAM		= 3;
	
	
	/**
	 * 保证对象不被clone
	 */
	private function __clone() {}

    /**
	 * 构造函数
	 */
	private function __construct() {}


	/**
	 * HTTP工厂操作方法
	 *
	 * @param string $url 需要访问的URL
	 * @param int $type 需要使用的HTTP类
	 * @return object
	 */
	public static function factory($url = '', $type = self::TYPE_SOCK){
		if ($type == ''){
			$type = self::TYPE_SOCK;
		}
		switch($type) {
			case self::TYPE_CURL :
				if (!function_exists('curl_init')){
					throw new TM_Exception(__CLASS__ . " PHP CURL extension not install");
				}
				$obj = TM_Http_Curl::getInstance($url);
				break;
			case self::TYPE_SOCK :
				if (!function_exists('fsockopen')){
					throw new TM_Exception(__CLASS__ . " PHP function fsockopen() not support");
				}				
				$obj = TM_Http_Sock::getInstance($url);
				break;
			case self::TYPE_STREAM :
				if (!function_exists('stream_context_create')){
					throw new TM_Exception(__CLASS__ . " PHP Stream extension not install");
				}				
				$obj = TM_Http_Stream::getInstance($url);
				break;
			default:
				throw new TM_Exception("http access type $type not support");
		}
		return $obj;
	}
	
	
	/**
	 * 生成一个供Cookie或HTTP GET Query的字符串
	 *
	 * @param array $data 需要生产的数据数组，必须是 Name => Value 结构
	 * @param string $sep 两个变量值之间分割的字符，缺省是 & 
	 * @return string 返回生成好的Cookie查询字符串
	 */
	public static function makeQuery($data, $sep = '&'){
		$encoded = '';
		while (list($k,$v) = each($data)) { 
			$encoded .= ($encoded ? "$sep" : "");
			$encoded .= rawurlencode($k)."=".rawurlencode($v); 
		} 
		return $encoded;		
	}
}
?>