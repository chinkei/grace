<?php
class encrypt
{
	public $hash_key;
	public $hash_length;
	public $salt = 'test';
	
	function __construct() {
       $this->hash_key = Frame_Config::get('config.encryption_key');
       $this->hash_length = strlen($this->hash_key);
    }
    
    public static function getInstance(){
    	static $obj;
    	if(!$obj) $obj = new self();
    	return $obj;
    }
        
    
    function encrypt($string) {
    	$out = '';
    	$rand = $this->rand();
    	for($i=0; $i < $this->hash_length; $i++){
    		$out .= chr(ord($rand[$i]) ^ ord($this->hash_key[$i]));
    	}
    	
    	$key = $rand;
    	$str_len = strlen($string);
    	for($i=0; $i < $str_len; $i++) {
    		$out .= chr(ord($key[$i%$this->hash_length]) ^ ord($string[$i]));
    	}
        return base64_encode($out);
    }
    
    function decrypt($string) {
    	$string = base64_decode($string);
    	$strlen = strlen($string);
		
    	if($strlen <= $this->hash_length) {
			return FALSE;
		}
    	$encrypt_rand   = substr($string, 0, $this->hash_length);
    	$encrypt_string = substr($string, $this->hash_length, $strlen);
    	
        $rand = '';
    	for($i = 0; $i < $this->hash_length; $i++){
    		$rand .= chr(ord($encrypt_rand[$i]) ^ ord($this->hash_key[$i]));
    	}
		
    	$out = '';
    	$key = $rand;
    	$str_len = strlen($encrypt_string);
    	for($i = 0; $i < $str_len; $i++){
    		$out .= chr(ord($key[$i%$this->hash_length]) ^ ord($encrypt_string[$i]));
    	}
    	return $out;
    	
       
    }

   function rand(){
   		return substr(md5(uniqid(rand(1, 100000).$this->salt)),0,$this->hash_length);
   }
   
   function base64clean($base64string)
   {
   		$base64string = str_replace(array('=','+','/'),'',$base64string);
   
   		return $base64string;
   }
}
?>