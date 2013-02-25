<?php
class Grace_Network_Request_Curl extends Grace_Network_Request
{
	
	/**
	 * 需要访问的URL地址
	 * 
	 * @var string
	 */
	private $_curl = NULL;
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->_curl_open();
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct()
	{
		// 关闭CURL
		curl_close($this->_curl);
	}
	
	/**
	 * 
	 */
	public function cookie()
	{
		if (! isset ( $this->_cookie )) {
			if (! empty ( $this->_cookie ) && $this->_is_temp_cookie && is_file ( $this->_cookie )) {
				unlink ( $this->_cookie );
			}
			
			$this->_cookie = tempnam ( $this->_options['temp_root'], 'curl_manager_cookie_' );
			$this->_is_temp_cookie = TRUE;
		}
		
		curl_setopt ( $this->_ch, CURLOPT_COOKIEJAR, $this->_cookie );
		curl_setopt ( $this->_ch, CURLOPT_COOKIEFILE, $this->_cookie );
		
		return $this;
	}
	
	/**
	 * 
	 */
	public function ssl()
	{
		// 不对认证证书来源进行检查
		curl_setopt($this->_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		// 检查SSL加密算法是否存在
		curl_setopt($this->_curl, CURLOPT_SSL_VERIFYHOST, TRUE);
		return $this;
	}
	
	public function setProxy($host, $port, $type = 'HTTP', $options = array())
	{
		curl_setopt($this->_curl, CURLOPT_PROXYTYPE, $type ? CURLPROXY_HTTP : CURLPROXY_SOCKS5;);
		curl_setopt($this->_curl, CURLOPT_PROXY, $host);
		curl_setopt($this->_curl, CURLOPT_PROXYPORT, $port);
		
		$options = array_merge(array('auth' => '', 'user' => '', 'pwd'  => ''), $options);
		
		// 代理要认证
		if ( $options['auth'] ) {
			curl_setopt($this->_curl, CURLOPT_PROXYAUTH, $options['auth'] == 'BASIC' ? CURLAUTH_BASIC : CURLAUTH_NTLM);
			curl_setopt($this->_curl, CURLOPT_PROXYUSERPWD, "[{$options['user']}]:[{$options['pwd']}]");
		}
		return $this;
	}
	
	/**
	 * 开启CURL
	 * 
	 * @return void
	 */
	protected function _curl_open()
	{
		$this->_curl = curl_init();
		
		// 启用时会将服务器服务器返回的"Location:"放在header中递归的返回给服务器
		curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, TRUE);
		
		// 设置http头,支持lighttpd服务器的访问
		curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array('Expect:'));
		
		// 是否将头文件的信息作为数据流输出(HEADER信息),这里保留报文
		curl_setopt($this->_curl, CURLOPT_HEADER, TRUE);
		
		// 获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->_curl, CURLOPT_BINARYTRANSFER, TRUE);
	}
	
	/**
	 * 发送HTTP请求核心函数
	 *
	 * @param string $method 使用GET还是POST方式访问
	 * @param array $vars 需要另外附加发送的GET/POST数据
	 * @param int $timeout 连接对方服务器访问超时时间，单位为秒
	 * @param array $options 当前操作类一些特殊的属性设置
	 * @return string 返回服务器端读取的返回数据
	 */
	public function send($method = 'GET', $timeout = 5, $options = array())
	{
		//处理参数是否为空
		if ($this->_uri == ''){
			throw new TM_Exception(__CLASS__ .": Access url is empty");
		}
			
		// 初始化CURL
        $ch = $this->_curl_open($timeout);
        
        //设置特殊属性
        if (!empty($options)){
        	curl_setopt_array($ch , $options);
        }        
        //处理GET请求参数
        if ($method == 'GET' && !empty($this->vars)){
        	$query = TM_Http::makeQuery($this->vars);
        	$parse = parse_url($this->uri);
        	$sep = isset($parse['query'])  ?  '&'  : '?';
        	$this->uri .= $sep . $query;
        }
        //处理POST请求数据
        if ($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, 1 );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->vars);
        }
        
        //设置cookie信息
        if (!empty($this->cookies)){
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookies);
        }
        //设置HTTP缺省头
        if (empty($this->header)){
	        $this->header = array(
	        	'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)',
		        //'Accept-Language: zh-cn',	        	
	        	//'Cache-Control: no-cache',
	        );
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);

        //发送请求读取输数据
        curl_setopt($ch, CURLOPT_URL, $this->uri);        
        $data = curl_exec($ch);
        if( ($err = curl_error($ch)) ){
            curl_close($ch);
			throw new  Exception(__CLASS__ ." error: ". $err);
        }
        curl_close($ch);
        return $data;
	}
}
?>