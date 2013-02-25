<?php
class GR_Model
{
	
	// 执行SQL语句
	public function query($sql)
	{
		if ( empty($sql) ) {
			return FALSE
		}
		return $this->db->query($sql);
	}
	
	public function find()
	{
		
	}
	
	public function findAll()
	{
		
	}
	
	// 获取上一次执行sql语句
	public function getLastSql()
	{
		
	}
	
	// 获取上一次执行insert的sql语句自增ID
	public function getInsertId()
	{
		
	}
	
	// 获取数据库错误信息
	public function getDbError(){
	}
	
	// 事务操作 
	public function transStart()
	{
		
	}
	
	public function commit()
	{
		
	}
	
	public function rollback()
	{
		
	}
}
?>