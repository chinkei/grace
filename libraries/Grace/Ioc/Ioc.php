<?php
class Grace_Ioc_Ioc
{
	protected static $_register   = array();
	protected static $_singletons = array();
	
	public static function register($name, $optiones, $is_single = FALSE)
	{
		if ( is_string($name) && $name != '' ) {
			self::$_register[$name] = compact('optiones', 'is_single');
			return TRUE;
		}
		return FALSE;
	}
	
	public static function remove($name)
	{
		if ( self::isRegistered($name) ) {
			self::$_register[$name] = NULL;
			unset(self::$_register[$name]);
		}
		return TRUE;
	}
	
	public static function isRegistered($name)
	{
		return array_key_exists($name, self::$_register);
	}
	
	public static function setInstance($name, $instance)
	{
		self::$_singletons[$name] = $instance;
	}
	
	public static function resolve($name)
	{
		if (isset(self::$_singletons[$name])) {
			return self::$_singletons[$name];
		}
		
		if ( isset(self::$_register[$name]) ) {
			$optiones = self::$_register[$name]['optiones'];
			
			if ( isset($optiones['class']) ) {
				// 创建实例
				isset($optiones['path']) ? uses($optiones['class'], $optiones['path']) : uses($optiones['class']);
				
				if ( isset($optiones['params']) ) {
					$arguments = (array)$optiones['params'];
					$reflection = new ReflectionClass($optiones['class']);
					$object     = $reflection->newInstanceArgs($arguments);
				} else {
					$object = new $optiones['class'];
				}
				
				if ( self::$_register[$name]['is_single'] === TRUE ) {
					$_singletons[$name] = $object;
				}
				return $object;
				
			} else {
				self::remove($name);
				return FALSE;
			}
		}
		return FALSE;
	}
}
?>