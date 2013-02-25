<?php
class GR_Output
{
	private $_buffers    = array();
	private $_headers    = array();
	private $_httpStatus = NULL;
	private $_mimes      = NULL;
	
	/**
	 * 新增HTTP header
	 * 
	 * @param $header
	 */
	public function addHeader($header)
	{
		$this->_headers[] = $header;
	}
	
	public function dumpHeaders()
	{
		foreach ($this->_headers as $header) {
			$this->sendHeader($header, TRUE);
		}
		unset($this->_headers);
		$this->_headers = array();
	}
	
	public function dumpBuffers()
	{
		foreach ($this->_buffers as $buffer) {
			echo $buffer;
		}
		unset($this->_buffers);
		$this->_buffers = array();
	}
	
	public function dump()
	{
		$this->dumpHeaders();
		$this->dumpBuffers();
	}
	
	public function clearBuffers()
	{
		unset($this->_buffers);
		$this->_buffers = array();
	}
	
	public function startBuffer()
	{
		ob_start();
	}
	
	public function endBuffer()
	{
		$this->pauseBuffer();
		$this->dump();
	}
	
	public function pauseBuffer()
	{
		if (ob_get_length() > 0) {
			$this->_buffers[] = ob_get_contents();
			ob_end_clean();
		}
	}
	
	public function sendHeader($code, $replace = TRUE)
	{
		if (is_numeric($code)) {
			self::statusHeader($code);
		} else {
			header($code, $replace);
		}
	}
	
	public function statusHeader($code, $replace = TRUE)
	{
		if (is_numeric($code)) {
			if (NULL === $this->_httpStatus) {
				$httpFile = APP_PATH.'config/http.php';
				if ( ! file_exists($httpFile)) {
					exit('The http file does not exist.');
				}
				
				require($httpFile);
				
				if ( ! isset($http) || ! is_array($http)) {
					exit('Your http file does not appear to be formatted correctly.');
				}
				
				$this->_httpStatus = $http;
				unset($http);
			}
			$code = strval($code);
			$statusText = isset($this->_httpStatus[$code]) ? $this->_httpStatus[$code] : NULL;
			NULL !== $statusText && header("HTTP/1.1 {$code} {$statusText}", $replace);
		}
	}
	
	public function getMimes($file)
	{
		if (NULL === $this->_mimes) {
			$mimesFile = APP_PATH.'config/mimes.php';
			if ( ! file_exists($mimesFile)) {
				exit('The mimes file does not exist.');
			}
			
			require($mimesFile);
				
			if ( ! isset($mimes) || ! is_array($mimes)) {
				exit('Your mimes file does not appear to be formatted correctly.');
			}
			
			$this->_mimes = $mimes;
			unset($mimes);
		}
		
		$ext = strtolower(substr(strrchr($fileName, '.'), 1));
		
		if(isset($this->_mimes[$ext])){
			return $this->_mimes[$ext];
		}
		return 'application/octet-stream';
	}
}
?>