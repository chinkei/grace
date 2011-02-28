<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 安全处理类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Security
 */
class Grace_Security_Security
{
	const SALT       = "this#is#a#salt";
	const INPUT_NAME = 'security_input_valid'
	
	/**
	 * csrf 表单
	 * @param string $form_input_name
	 * @return string
	 */
	public static function csrf_form()
	{
		$time = time();
		$hash = $time.md5($time.self::SALT);
		
		$str = "<input type='hidden' name='".self::INPUT_NAME."' value='{$hash}'>";
		Lib_Session::getInstance()->set(self::INPUT_NAME."_".$time, 1);
		return $str;
	}
	
	/**
	 * csrf 校验,表单防刷
	 * @return boolean
	 */
	public static function csrf_check()
	{
		if ( !isset($_POST[self::INPUT_NAME]) ) {
			return FALSE;
		}
		$strCsrf = $_POST[self::INPUT_NAME];
		
		$time = substr($strCsrf, 0, 10);
		$hash = $time.md5($time.self::SALT);
		
	    if($strCsrf != $hash) {
			return FALSE;
		}
		
	    $is_set =  Lib_Session::getInstance()->is_set(self::INPUT_NAME."_".$time);
		
		if ( !$is_set ){
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * 安全过滤
	 * @param $string 字符或数组
	 */
	public function cleanXss($string)
	{
		if ( ! is_array($string) ) {
			return $this->xss($string);
		} else {
			foreach ($string as $key => $val) {
				$string[$key] = $this->cleanXss($val);
			}
		}
		return $string;
	}
	
	/**
	 * 字符转换为 HTML 实体
	 * @param  $string
	 */
	public function safeHtml($string) {
	
		if ( ! is_array($string) ) {
			return trim(htmlspecialchars($this->xss($string)));
		} else {
			foreach ($string as $key => $val) {
				$string[$key] = $this->safeHtml($val);
			}
		}
		return $string;
	}
	
	/**
	 * 过滤XSS（跨站脚本攻击）的函数
	 * 
	 * @param string $val 字符串参数，可能包含恶意的脚本代码如<script language="javascript">alert("hello world");</script>
	 * @return 处理后的字符串
	 **/
	private function xss($val)
	{
		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		$val = preg_replace ( '/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val );
		
		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=@avascript:alert('XSS')>
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for($i = 0; $i < strlen ( $search ); $i ++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
			

			// @ @ search for the hex values
			$val = preg_replace ( '/(&#[xX]0{0,8}' . dechex ( ord ( $search [$i] ) ) . ';?)/i', $search [$i], $val ); // with a ;
			// @ @ 0{0,7} matches '0' zero to seven times
			$val = preg_replace ( '/(&#0{0,8}' . ord ( $search [$i] ) . ';?)/', $search [$i], $val ); // with a ;
		}
		
		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = array (
			'javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 
			'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base' 
		);
		
		$ra2 = array (
			'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 
			'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 
			'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend',
			'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 
			'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 
			'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste',
			'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete',
			'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload' 
		);
		$ra = array_merge ( $ra1, $ra2 );
		
		$found = true; // keep replacing as long as the previous round replaced something
		while ( $found == true ) {
			$val_before = $val;
			for($i = 0; $i < sizeof ( $ra ); $i ++) {
				$pattern = '/';
				for($j = 0; $j < strlen ( $ra [$i] ); $j ++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}
					$pattern .= $ra [$i] [$j];
				}
				$pattern .= '/i';
				$replacement = substr ( $ra [$i], 0, 2 ) . '<x>' . substr ( $ra [$i], 2 ); // add in <> to nerf the tag
				$val = preg_replace ( $pattern, $replacement, $val ); // filter out the hex tags
				if ($val_before == $val) {
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
		return $val;
	}
}
?>