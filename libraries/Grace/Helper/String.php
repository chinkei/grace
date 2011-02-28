<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 字符处理助手
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Helper
 */
 
/**
 * 取得随机字符串
 *
 * @param  string $length 字符个数
 * @return string
 */
if ( !function_exists('str_random') ) {
	function str_random($length = 4)
	{
		$strSource = '23456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$strMaxLen = strlen($strSource);
		
		$retString = '';
		for ($i = 0; $i < $len; $i++)
		{
			$randIndex = mt_rand(0, $strMaxLen - 1 );
			$retString .= $strSource{$randIndex};
		}
		
		return $retString;
	}
}

/**
 * 去除引号
 *
 * @param  string $str 要处理的字符串
 * @return string
 */
if ( ! function_exists('str_clean_quotes'))
{
	function str_clean_quotes($str)
	{
		return str_replace(array('"', "'"), '', $str);
	}
}

/**
 * 去除数据斜杠转义
 *
 * @param  string|array 
 * @return string|array
 */
if ( ! function_exists('str_slashes'))
{
	function str_slashes($data)
	{
		if (is_array($data)) {
			foreach ($data as $key => $val)
			{
				$data[$key] = str_slashes($val);
			}
		} else {
			$data = stripslashes($data);
		}

		return $data;
	}
}
?>