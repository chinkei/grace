<?php
class Cookie
{
	public $expire = NULL;
	public $path = '/';
	public $domain = '';
	public $secure;
	public $httponly;
	
	public function init($options = array())
	{
		if (is_array($options)) {
			foreach ($options as $option => $vel) {
			$this->$option = $val;
		} else {
			// TODO
		}
	}
	
	/**
	 * set 变量
	 * @param string     $name
	 * @param string|int $value
	 * @return boolean
	 */
	public function set($name, $value, $expire = 3600){
		$_COOKIE[$name] = $value;
		$this->expire   = time() + $expire;
		return setcookie($name,$value,$this->expire,$this->path,$this->domain,$this->secure,$this->httponly);
	}
	
	/**
	 * get 变量
	 * @param string $name
	 * @return bool
	 */
	public function get($name){
		if($this->is_set($name)) {
			return $_COOKIE[$name];
		}
		return FALSE;
	}
	
	/**
	 * 判断name是否存在
	 * @param string $name
	 */
	public function is_set($name){
		if(isset($_COOKIE[$name])){
			return true;
		}
		return false;
	}
	
	/**
	 * 删除cookie
	 * @param string $name
	 */
	public function del($name){
		unset($_COOKIE[$name]);
		return $this->set($name, NULL, -1);
	}
	
	/**
	 * 清空cookie
	 */
	public function destory(){
		$cookie = $_COOKIE;
		if ($cookie){
			foreach($cookie as $name=>$value){
				$this->del($name);
			}
			
		}
	}
	
	
}
?>