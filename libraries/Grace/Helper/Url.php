<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 文件下载处理助手
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Helper
 */
 
/**
 * 下载文件
 *
 * @param  string $path 路径
 * @return bool
 */
if ( !function_exists('url_segments') ) {
	function url_segments($index = '', $isAll = FALSE)
	{
		return get_instance()->input->getParams($index, $isAll);
	}
}
?>