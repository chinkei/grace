<?php
class Grace_IO_Output
{ 
	protected static $_instance = NULL;
	
	/**
     * Holds known mime type mappings
     *
     * @var array
     */
    protected $_statusCodes = array( 
        100 => 'Continue', 
        101 => 'Switching Protocols', 
        200 => 'OK', 
        201 => 'Created', 
        202 => 'Accepted', 
        203 => 'Non-Authoritative Information', 
        204 => 'No Content', 
        205 => 'Reset Content', 
        206 => 'Partial Content', 
        300 => 'Multiple Choices', 
        301 => 'Moved Permanently', 
        302 => 'Found', 
        303 => 'See Other', 
        304 => 'Not Modified', 
        305 => 'Use Proxy', 
        307 => 'Temporary Redirect', 
        400 => 'Bad Request', 
        401 => 'Unauthorized', 
        402 => 'Payment Required', 
        403 => 'Forbidden', 
        404 => 'Not Found', 
        405 => 'Method Not Allowed', 
        406 => 'Not Acceptable', 
        407 => 'Proxy Authentication Required', 
        408 => 'Request Timeout', 
        409 => 'Conflict', 
        410 => 'Gone', 
        411 => 'Length Required', 
        412 => 'Precondition Failed', 
        413 => 'Request Entity Too Large', 
        414 => 'Request-URI Too Long', 
        415 => 'Unsupported Media Type', 
        416 => 'Requested Range Not Satisfiable', 
        417 => 'Expectation Failed', 
        500 => 'Internal Server Error', 
        501 => 'Not Implemented', 
        502 => 'Bad Gateway', 
        503 => 'Service Unavailable', 
        504 => 'Gateway Timeout', 
        505 => 'HTTP Version Not Supported' 
    );
	
	/**
     * Holds known mime type mappings
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'ai' => 'application/postscript',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'ccad' => 'application/clariscad',
        'cdf' => 'application/x-netcdf',
        'class' => 'application/octet-stream',
        'cpio' => 'application/x-cpio',
        'cpt' => 'application/mac-compactpro',
        'csh' => 'application/x-csh',
        'csv' => 'text/csv',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'drw' => 'application/drafting',
        'dvi' => 'application/x-dvi',
        'dwg' => 'application/acad',
        'dxf' => 'application/dxf',
        'dxr' => 'application/x-director',
        'eot' => 'application/vnd.ms-fontobject',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'ez' => 'application/andrew-inset',
        'flv' => 'video/x-flv',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'bz2' => 'application/x-bzip',
        '7z' => 'application/x-7z-compressed',
        'hdf' => 'application/x-hdf',
        'hqx' => 'application/mac-binhex40',
        'ico' => 'image/vnd.microsoft.icon',
        'ips' => 'application/x-ipscript',
        'ipx' => 'application/x-ipix',
        'js' => 'text/javascript',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'lsp' => 'application/x-lisp',
        'lzh' => 'application/octet-stream',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'mif' => 'application/vnd.mif',
        'ms' => 'application/x-troff-ms',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'otf' => 'font/otf',
        'pdf' => 'application/pdf',
        'pgn' => 'application/x-chess-pgn',
        'pot' => 'application/mspowerpoint',
        'pps' => 'application/mspowerpoint',
        'ppt' => 'application/mspowerpoint',
        'ppz' => 'application/mspowerpoint',
        'pre' => 'application/x-freelance',
        'prt' => 'application/pro_eng',
        'ps' => 'application/postscript',
        'roff' => 'application/x-troff',
        'scm' => 'application/x-lotusscreencam',
        'set' => 'application/set',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'sol' => 'application/solids',
        'spl' => 'application/x-futuresplash',
        'src' => 'application/x-wais-source',
        'step' => 'application/STEP',
        'stl' => 'application/SLA',
        'stp' => 'application/STEP',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tr' => 'application/x-troff',
        'tsp' => 'application/dsptype',
        'ttf' => 'font/ttf',
        'unv' => 'application/i-deas',
        'ustar' => 'application/x-ustar',
        'vcd' => 'application/x-cdlink',
        'vda' => 'application/vda',
        'xlc' => 'application/vnd.ms-excel',
        'xll' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'zip' => 'application/zip',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'au' => 'audio/basic',
        'kar' => 'audio/midi',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mpga' => 'audio/mpeg',
        'ogg' => 'audio/ogg',
        'ra' => 'audio/x-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'snd' => 'audio/basic',
        'tsi' => 'audio/TSP-audio',
        'wav' => 'audio/x-wav',
        'asc' => 'text/plain',
        'c' => 'text/plain',
        'cc' => 'text/plain',
        'css' => 'text/css',
        'etx' => 'text/x-setext',
        'f' => 'text/plain',
        'f90' => 'text/plain',
        'h' => 'text/plain',
        'hh' => 'text/plain',
        'html' => 'text/html',
        'htm' => 'text/html',
        'm' => 'text/plain',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'tsv' => 'text/tab-separated-values',
        'tpl' => 'text/template',
        'txt' => 'text/plain',
        'text' => 'text/plain',
        'xml' => 'application/xml',
        'avi' => 'video/x-msvideo',
        'fli' => 'video/x-fli',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'viv' => 'video/vnd.vivo',
        'vivo' => 'video/vnd.vivo',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'ppm' => 'image/x-portable-pixmap',
        'ras' => 'image/cmu-raster',
        'rgb' => 'image/x-rgb',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'ice' => 'x-conference/x-cooltalk',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'mesh' => 'model/mesh',
        'msh' => 'model/mesh',
        'silo' => 'model/mesh',
        'vrml' => 'model/vrml',
        'wrl' => 'model/vrml',
        'mime' => 'www/mime',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-pdb',
        'javascript' => 'text/javascript',
        'json' => 'application/json',
        'form' => 'application/x-www-form-urlencoded',
        'file' => 'multipart/form-data',
        'xhtml' => 'application/xhtml+xml',
        'xhtml-mobile' => 'application/vnd.wap.xhtml+xml',
        'rss' => 'application/rss+xml',
        'atom' => 'application/atom+xml',
        'amf' => 'application/x-amf',
        'wap' => 'text/vnd.wap.wml',
        'wml' => 'text/vnd.wap.wml',
        'wmlscript' => 'text/vnd.wap.wmlscript',
        'wbmp' => 'image/vnd.wap.wbmp',
    );
	
	/**
     * HTTP 协议
     *
     * @var string
     */
    protected $_protocol = 'HTTP/1.1';
	
	
	/**
     * 头信息数组
     *
     * @var array
     */
	protected $_headers = array();
	
	protected $_body = '';

    /**
     * cookie信息数组
     * 
     * @var array
     */
    protected $_cookies = array();
	
    /**
     * 编码类型
     *
     * @var string
     */
    protected $_charset = 'UTF-8';
	
	private function __construct(){}
	private function __clone(){}
	
	public static function getInstance()
	{
		if (self::$_instance == NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 页面输出
	 * 
	 * @return void
	 */
	public function send()
	{
		$this->_sendHeader();
		$this->_sendBody();
	}
	
	/**
	 * 设置页面输出编码类型
	 * 
	 * @param  string $charset 编码类型
	 * @return void
	 */
	public function setCharset($charset)
	{
		$this->_charset = $charset;
	}
	
	public function setProtocol($protocol = '1.1')
	{
		$this->_protocol = 'HTTP/' . $protocol;
	}
	
	/**
	 * 增加头信息
	 * 
	 * @param  string $key   键
	 * @param  string $value 值
	 * @return void
	 */
	public function addHeader($key, $value)
	{
		$this->_headers[$key] = $value;
	}
	
	/**
	 * 移除指定头信息
	 * 
	 * @param  string $key 键值
	 * @return bool
	 */
	public function removeHeader($key)
	{
		if (isset($this->_headers[$key])) {
			unset($this->_headers[$key]);
		}
		return TRUE;
	}
	
	/**
	 * 设置mimeTypes信息
	 * 
	 * @param  string|array $contentType
	 * @return bool
	 */
	public function setContentType($contentType = NULL)
	{
		if ($contentType == NULL) {
			return FALSE;
		}
		
		$contentText = '';
		
		if (is_array($contentType)) {
            $contentText = current($contentType);
			$contentType = key($contentType);
		} else {
			if (isset($this->_mimeTypes[$contentType])) {
				$contentText = $this->_mimeTypes[$contentType];
			}
		}
		
		$contentText = ( $contentText == '' ? 'text/html' : $contentText);
		
        if (strpos($contentText, 'text/') === 0) {
            $this->addHeader('Content-Type', "{$contentText}; charset={$this->_charset}");
        } else {
            $this->addHeader('Content-Type', "{$contentText}");
        }
		return TRUE;
	}
	
	/**
	 * 跳转页面
	 *
	 * @param  string $url 要跳转的URL地址
	 * @return void
	 */
	public function redirect($url)
	{
		header('Location: ' . $url);    
		exit();
	}
	
	/**
	 * 发送http响应码
	 * 
	 * @param  int  $headerCode 响应码
	 * @param  bool $replace    是否替换
	 * @return bool
	 */
	public function statusHeader($headerCode, $replace = TRUE)
	{
		if ( is_numeric($headerCode) ) {
			$statusText = NULL;
			if (isset($this->_statusCodes[$headerCode])) {
				$statusText = $this->_statusCodes[$headerCode];
			}
			
			if ( NULL !== $statusText) {
				header("{$this->_protocol} {$headerCode} {$statusText}", $replace, $headerCode);
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function setBody($data)
	{
		$this->_body  = $data;
	}
	
	/**
	 * 压缩字符串
	 *
	 * @param  string $data 字符数据
	 * @return string
	 */
	public function compress($data, $level = 9)
	{
		// 是否使用压缩
		if ( $level === FALSE || $level < 1) {
			return $data;
		}
		$encoding = FALSE;
		
		if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
			if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
				$encoding = 'gzip';
			}
			if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== FALSE) {
				$encoding = 'x-gzip';
			}
		}
		if ($encoding === FALSE || !extension_loaded('zlib') || ini_get('zlib.output_compression') ) {
			return $data;
		}
		
		if (connection_status() != 0 || headers_sent() ) {
			return $data;
		}
		
		header("Content-Encoding: " . $encoding);
		header("Vary: Accept-Encoding");
		header("Content-Length: ".strlen($data));

		// 最大压缩级别为9
		$level = ( $level > 9 ? 9 : $level);
		return gzencode($data, (int)$level);
	}
	
	/**
 	 * 发送头信息
	 * 
	 * @return bool
	 */
	protected function _sendHeader()
	{
		if ( !headers_sent() ) {
			foreach ($this->_headers as $key => $val) {
				header($key . ': ' . $val);
			}
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 发送body信息
	 * 
	 * @return void
	 */
	protected function _sendBody()
	{
		echo $this->_body;
	}
} 