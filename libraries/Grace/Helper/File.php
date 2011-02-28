<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 文件及文件加助手
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Helper
 */
 
/**
 * 创建文件夹
 *
 * @param  string $path 路径
 * @return bool
 */
if ( !function_exists('dir_create') ) {
	function dir_create($path)
	{
		if ( is_dir($path) ) {
			return TRUE;
		}
		
		$arrPath = array();
		while ( !is_dir($path) && $path != '' ) {
			$arrPath[] = $path;
			$path = dirname($path);
		}
		
		for ($i = count($arrPath); $i >= 0, $i--) {
			mkdir($arrPath[$i]);
		}
		
		return TRUE;
	}
}

/**
 * 删除指定目录下的文件及文件夹
 *
 * @param  string $path 路径
 * @return bool
 */
if ( !function_exists('dir_delete') ) {
	function dir_delete($path)
	{
		if ( !is_dir($path) ) {
			return TRUE;
		}
		
		if ( ($handle = opendir($dir)) !== FALSE ) {
			while ( ($file = readdir($handle)) !== FALSE ) {
				if ($file != '.' && $file != '..') {
					$childPath = $path . '/' . $file;
					is_dir($childPath) ? dir_delete($childPath) : unlink($childPath);
				}
			}
			closedir($handle);
			rmdir($path);
		}
		return TRUE;
	}
}

/**
 * 返回格式化后的文件大小
 * 
 * @param int $size byte大小
 * @param int $prec 保留小数的精度
 */
if ( !function_exists('size_format') ) {
	function size_format($size, $prec = 2)
	{
		foreach (array('B', 'KB', 'MB', 'GB', 'TB', 'PB') as $k => $v ) {
			if ($size < pow(1024, $k + 1)) {
				break;
			}
		}
		return round($size / pow(1024, $k), $prec) . $v;
	}
}
?>