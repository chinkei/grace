<?php
/**
 * 引入文件
 * TODO   该方法只适用于后缀.php的文件(不适用.class.php, .inc.php后缀文件)
 * 
 * @param  string $path       路径符以'.'号隔开,不包含后缀 如a.b.c Like a/b/c.php
 * @param  bool   $isRequire  true: require(), false:include();
 * @param  string $base       根目录(可以是框架路径,也可以是项目路径)
 * @return mixed
 */
function import_file($path, $isRequire = TRUE, $base = LIB_PATH)
{
	return Grace_Application_Application::import_file($path, $isRequire, $base);
}

function load_class($class, $isRequire = TRUE, $base = LIB_PATH)
{
	static $_load_class = array();
	
	if ( ! isset($_load_class[$class]) ) {
	
		if ( FALSE === use_class($class, $isRequire, $base) ) {
			return FALSE;
		}
		
		$_instace = new $class;
		is_object($_instace) && $_load_class[$class] = $_instace;
	}
	
	return $_load_class[$class];
}

function use_class($class, $isRequire = TRUE, $base = LIB_PATH)
{
	$arrPath  = explode('_', trim($class, '_'));
	$filePath = $base . '/' . implode('/', array_map('ucfirst', $arrPath) ) . '.php';
	
	if ( ! file_exists($filePath) ) {
		throw new Exception('file : "' . implode('/', array_map('ucfirst', $arrPath) ) . '" is not exists');
	}
	return ( $isRequire === TRUE ) ? require $filePath : include $filePath;
}

function get_instance($class, $isNew = FALSE)
{
	
}

function uses($className, $location = LIB_PATH)
{
	Grace_Application_Application::uses($className, $location);
}
?>