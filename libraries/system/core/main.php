<?php
/**
 * PHP版本比较
 *
 * @param string $version 版本号
 */
function php_version ($version)
{
	return (version_compare(PHP_VERSION, $version, '<=')) ? true : false;
}

/**
 * 开启注册全局变量时, 删除自动注册的全局变量（安全考虑）
 */
if (function_exists('ini_get') && ini_get('register_globals')) {
	/**
 	 * 删除自动注册的全局变量
     */
	function deregister_globals()
	{
		$autoRegister = array_keys($_REQUEST);

		foreach ($autoRegister as $val) {
			$$val = null;
			unset($$val);
		}
	}
	
	deregister_globals();
}

/**
 * 引入文件
 * TODO   该方法只适用于后缀.php的文件(不适用.class.php, .inc.php后缀文件)
 * 
 * @param  string $path       路径符以'.'号隔开,不包含后缀 如a.b.c Like a/b/c.php
 * @param  bool   $isRequire  true: require(), false:include();
 * @param  string $base       根目录(可以是框架路径,也可以是项目路径)
 * @return mixed
 */
function import_file($path, $isRequire = TRUE, $base = SYS_PATH)
{
	static $is_imported = array();
	
	$fileName = rtrim($base, '/') . '/' . str_replace('.', '/', $path);
	
	if (!in_array($fileName, $is_imported, TRUE)) {
		$fileFullPath = $fileName . '.php';
		if (file_exists($fileFullPath)) {
			$is_imported[] = $fileName;
			return $isRequire ? require ($fileFullPath) : include ($fileFullPath);
		}
	}
	return FALSE;
}

/**
 * 字节转换
 *
 * @param  int $byte 字节数
 * @param  int $len  精度长度
 * @return string 转换后单位
 */
function byte_convert($byte, $len = 2)
{
	$unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	foreach ($unit as $k => $v) {
		if ($byte < pow(1024, $k + 1)) {
			break;
		}
	}
	return round($size / pow(1024, $k), $len) . $v;
}

/**
 * 获取随机数
 * 
 * @param  int $len 长度
 * @return string 随机字符串
 */
function generateRand($len)
{
	$strSource = '23456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$strMaxLen = strlen($strSource);
	
	$retString = '';
	for ($i = 0; $i < $len; $i++) {
		$randIndex = mt_rand(0, $strMaxLen- 1 );
		$retString .= $strSource{$randIndex};
	}
	
	return $retString;
}

/**
 * 获取程序执行事件
 * 
 * @param  int $len 精度
 * @return string 执行时间
 */
function micro_time($len = 6)
{
	$temp = explode(' ', microtime());
	return bcadd($temp[0], $temp[1], $len)
}
?>