<?php
class GR_Register
{
	/**
	 * 注册对象数组 
	 * 
	 * @var array
	 */
	private static $_registers = array();
	
	
	/**
	 * 获取对象
	 * 
	 * @param  string $key     对象键值
	 * @param  mixed  $default 默认值
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		if (self::isExists($key)) {
			return self::$_registers[$key];
		}
		return $default;
	}
	
	/**
	 * 注册对象
	 * 
	 * @param  string  $key     对象键值
	 * @param  object  $value   对象实例
	 * @param  boolean $replace 是否替换重复键对象
	 * @return boolean
	 */
	public static function set($key, $instance, $replace = TRUE)
	{
		if (self::isExists($key) && $replace == FALSE) {
            trigger_error($key.' already set. Please use replace method.', E_USER_WARNING);
            return FALSE;
        }
		self::$_registers[$key] = $instance;
        return TRUE;
	}
	
	/**
	 * 数组对象注册
	 * 
	 * @param  string $key   数组对象
	 * @param  mixed  $value 是否替换重复键对象
	 * @return mixed
	 */
	public static function setArray($arr, $replace = TRUE)
    {
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                self::set($k, $v, $replace);
            }
        }
        return TRUE;
    }
	
	/**
	 * 替换指定键值对象
	 * 
	 * @param  string  $key      键值
	 * @param  object  $instance 对象实例
	 * @return boolean
	 */
	public static function replace($key, $instance)
    {
		if (self::isExists($key)) {
            self::$_registers[$key] = $instance;
        }
        return TRUE;
    }
    
	/**
	 * 移除指定键值对象
	 * 
	 * @param  string  $key 键值
	 * @return boolean
	 */
    public static function remove($key)
    {
        if (self::isExists($key)) {
            unset(self::$_registers[$key]);
        }
        return TRUE;
    }
	
	/**
	 * 获取所有对象实例
	 */ 
	public static function getAll()
	{
		return self::$_registers;
	}
	
	/**
	 * 根据键值检查对象实例是否存在
	 */
	public static function isExists($key = null)
	{
		return isset(self::$_registers[$key]);
	}
	
	/**
	 * 清空对象数组
	 */
	public static function clear()
	{
		self::$_registers = array();
	}
}
?>