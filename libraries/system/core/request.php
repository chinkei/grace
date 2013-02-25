<?php
class GR_Request
{
	private $_httpHeaders;
	private $_modeCache;
	private $_defaultMode;
	private $_customMode;
	
	public function __construct()
	{
		$this->_httpHeaders = array();
		$this->_modeCache   = array();
		$this->_customMode  = array();
		$this->_defaultMode = 'r';
	}
	
	public function setMode($mode, $data = array(), $isReplace = TRUE)
	{
		$mode = strtolower($mode);
		$data = $data;
		
		if (strlen($mode) != 1) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'(): mode \''.$mode.'\' is not a single character', E_USER_WARNING);
			return FALSE;
		}
		
		if ( in_array($mode, array('c', 'e', 'g', 'p', 'r', 's', 'f'))) {
			trigger_error(__CLASS__."::".__FUNCTION__."(): can not override default mode '{$mode}'", E_USER_WARNING);
			return FALSE;
		}
		
		if ( isset($this->_customMode[$mode]) ) {
			if (FALSE == $isReplace) {
				return FALSE;
			}
			$this->_RemoveModeCache($mode);
		}
		
		$this->_customMode[$mode] = $data;
		return TRUE;
	}
	
	/**
	 * 移除自定义模式数据
	 */
	public function removeMode($mode)
	{
		if (array_key_exists($mode, $this->_customMode)) {
			unset($this->_customMode[$mode]);
			$this->_removeModeCache($mode);
			return TRUE;
		}
		return TRUE;
	}
	
	public function setDefaultMode($mode)
	{
		$this->_defaultMode = $mode;
	}
	
	/**
	 * 获取http头部信息
	 */
	public function getHeader($name, $default = NULL)
	{
		if (array_key_exists($name, $this->_httpHeaders)) {
			return $this->_httpHeaders[$name];
		}
		return $default;
	}
	
	/**
 	 * 获取值
	 */
	public function getValue($name, $mode = NULL, $xss_clean = FALSE)
	{
		foreach ($this->_parseMode($mode) as $arr) {
			if (array_key_exists($name, $arr)) {
				if (TRUE === $xss_clean) {
					return Application::security()->cleanXss($arr[$name]);
				}
				return $arr[$name];
			}
		}
		return FALSE;
	}
	
	/**
 	 * get获取值
	 */
	public function get($name, $xss_clean = FALSE)
	{
		return $this->getValue($name, 'g', $xss_clean);
	}
	
	/**
 	 * get获取值
	 */
	public function post($name, $xss_clean = FALSE)
	{
		return $this->getValue($name, 'p', $xss_clean);
	}
	
	/**
 	 * get|post获取值
	 */
	public function get_post($name, $xss_clean = FALSE)
	{
		if ( ! isset($_POST[$name]) ) {
			return $this->getValue($name, 'g', $xss_clean);
		}
		return $this->getValue($name, 'p', $xss_clean);
	}
	
	/**
 	 * cookie获取值
	 */
	public function cookie($name, $xss_clean = FALSE)
	{
		return $this->getValue($name, 'c', $xss_clean);
	}
	
	/**
 	 * 解析数据模型
	 */
	private function _parseMode($mode)
	{
		$mode = ($mode ? $mode : $this->_defaultMode);
		$mode = strtolower($mode);
		
		if (array_key_exists($mode, $this->_modeCache)) {
			return $this->_modeCache[$mode];
		}
		
		$data = array();
		
		for ($i = 0; $i < strlen($mode); $i++) {
			// 存在自定义类型时
			if (array_key_exists($mode{$i}, $this->_customMode)) {
				$data[] =& $this->_customMode[$mode{$i}];
				continue;
			}
			
			switch($mode{$i}) {
				case 'c': $data[] =& $_COOKIE;  break;
				case 'e': $data[] =& $_ENV;     break;
				case 'g': $data[] =& $_GET;     break;
				case 'p': $data[] =& $_POST;    break;
				case 'r': $data[] =& $_REQUEST; break;
				case 'f': $data[] =& $_FILES;   break;
				case 'S': $data[] =& $_SERVER;  break;
				default:
					trigger_error(__CLASS__.'::'.__FUNCTION__.'() : 未知类型'.$mode{$i}, E_USER_WARNING);
				break;
			}
		}
		$this->_modeCache[$mode] = $data;
		
		return $data;
	}
	
	/**
	 * 移除模型缓存
	 */
	private function _removeModeCache($mode)
	{
		foreach (array_keys($this->_modeCache) as $key) {
			if (strpos($key, $mode) !== FALSE) {
				unset($this->_modeCache[$key]);
			}
		}
	}
	
	/**
	 * 解析http头部信息
	 */
	private function _parseHttpHeaders()
	{
		foreach ($_SERVER as $key => $vel) {
			if (substr($key, 0, 5) != 'HTTP_') {
				continue;
			}
			$header = implode( '_', array_map('ucfirst', explode('_', strtolower(substr($key, 5)))) );
			$this->_httpHeaders[$header] = $vel;
		}
	}
}
?>