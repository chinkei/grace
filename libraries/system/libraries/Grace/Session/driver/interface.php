<?php
interface ifc_session
{
	public function open($save_path, $session_name);
	
	public function close();
	
	public function read($session_id);
	
	public function write($session_id, $data);
	
	public function destory($session_id);
	
	public function gc($maxlifetime);
}
?>