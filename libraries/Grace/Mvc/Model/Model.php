<?php
abstract class Grace_Mvc_Model_Model
{	
	/**
	 * 当前控制器对象
	 *
	 * @var object
	 */
	protected $_conrtol = NULL;
	
	/**
	 * 当前数据库对象
	 * 
	 * @var object
	 */
	protected $_db      = NULL;
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->_db      = Grace_Database_Db::loadDriver();
		$this->_conrtol = get_instance();
	}
	
	/**
	 * 获取魔法变量
	 * 
	 * @param  string $name 变量名
	 * @return mixed 变量值
	 */
	public function __get($name)
    {
		if ($this->_conrtol != NULL && isset($this->_conrtol->{$name})) {
			return $this->_conrtol->{$name};
		}
		return FALSE;
    }
	
	
	/**
	 * 插入记录
	 * 
	 * @param  string $table 表名
	 * @param  array  $data  数据
	 */
	public function insert($table, $data = array())
	{
		return $this->_db->insert($table, $data);
	}
	
	/**
	 * 更新记录
	 * 
	 * @param  string $table 表名
	 * @param  array  $data  数据
	 * @param  mixed  $where 过滤条件(string|array)
	 */
	public function update($table, $data = array(), $where = NULL)
	{
		return $this->_db->update($table, $data, $where);
	}
	
	/**
	 * 删除记录
	 * 
	 * @param  string $table 表名
	 * @param  mixed  $where 过滤条件(string|array)
	 */
	public function delete($table, $where = NULL)
	{
		return $this->_db->delete($table, $where);
	}
}
?>