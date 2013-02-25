<?php
class Session_Memcache implements ifc_session
{
	private $_memcache;
	
	public function __construct()
	{
		
	}
	
	public function open($save_path, $session_name)
	{
		return true;
	}
	
	public function close()
	{
		
	}
	
	public function read($session_id)
	{
		
	}
	
	public function write($session_id, $data = '')
	{
		
	}
	
	public function destory($session_id)
	{
		
	}
	
	public function gc($maxlifetime = NULL)
	{
		
	}
}
?>