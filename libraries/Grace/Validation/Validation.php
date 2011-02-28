<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

uses('Grace_Security_Security');

/**
 * 表单验证类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Validation
 */
class Grace_Validation_Validation
{
	protected $_error_messages = array(
		'required'           => '%s 必须填写.',
		'valid_email'        => '%s 必须是一个有效的电子邮箱地址.',
		'valid_emails'       => '%s 必须是有效的电子邮箱地址.',
		'valid_url'          => '%s 必须是有效的网址.',
		'valid_ip'           => '%s 必须是一个有效IP地址.',
		'min_length'         => '%s 至少包含 %s 个字.',
		'max_length'         => '%s 不能超过 %s 个字.',
		'exact_length'       => '%s 必须刚好 %s 个字.',
		'alpha'              => '%s 只能包含英文字母.',
		'alpha_numeric'      => '%s 只能包含英文字母或数字.',
		'alpha_dash'         => '%s 只能包含英文字母、数字、下划线、或破折号.',
		'numeric'            => '%s 只能是数字.',
		'integer'            => '%s 只能是整数.',
		'regex_match'        => '%s 格式不正确.',
		'matches'            => '%s 与 %s 不相符合.',
		'is_natural'         => '%s 必须是自然数(非负整数).',
		'is_natural_no_zero' => '%s 必须是大于零的自然数(非负整数).',
	);

	protected $_field_data     = array();
	protected $_error_array    = array();
	protected $_valid_name     = '';
	protected $_controller     = NULL;
	
	/**
	 * 构造函数
	 * 
	 * @param  string $vaild_name 隐藏表单名
	 * @return void
	 */
	public function __construct($valid_name = Grace_Security_Security::INPUT_NAME)
	{
		$this->_valid_name = $valid_name;
		$this->_controller = get_instance();
	}
	
	/**
	 * 设置验证规则
	 *
	 * @param  string|array $field 字段
	 * @param  string       $lable 字段名称
	 * @param  string       $rules 验证规则
	 * @return bool
	 */
	public function set_rules($field, $lable = '', $rules = '')
	{
		if (count($_POST) == 0) {
			return FALSE;
		}
		
		if (is_array($field)) {
			foreach ($field as $row) {
				if ( ! isset($row['field']) || ! isset($row['rules']) ) {
					continue;
				}
				
				$label = ( ! isset($row['label']) ) ? $row['field'] : $row['label'];
				
				$this->set_rules($row['field'], $label, $row['rules']);
			}
			return TRUE;
		}
		
		if ( ! is_string($field) || ! is_string($rules) || $field == '' ) {
			return FALSE;
		}
		
		$label = ($label == '') ? $field : $label;

		// 该字段是否为一个数组
		if (strpos($field, '[') !== FALSE && preg_match_all('/\[(.*?)\]/', $field, $matches)) {
		
			$x = explode('[', $field);
			$indexes[] = current($x);

			for ($i = 0; $i < count($matches['0']); $i++)
			{
				if ($matches['1'][$i] != '')
				{
					$indexes[] = $matches['1'][$i];
				}
			}
			$is_array = TRUE;
		} else {
			$indexes	= array();
			$is_array	= FALSE;
		}

		// 构造字段数组
		$this->_field_data[$field] = array(
			'field'				=> $field,
			'label'				=> $label,
			'rules'				=> $rules,
			'is_array'			=> $is_array,
			'keys'				=> $indexes,
			'postdata'			=> NULL,
			'error'				=> ''
		);
		
		return TRUE;
	}
	
	/**
	 * 设置错误提示信息
	 * 
	 * @param  string|array $message 错误信息键值|错误信息数组
	 * @param  string       $val     错误信息
	 * @return void
	 */
	public function set_message($message, $val = '')
	{
		if ( ! is_array($message) ) {
			$message = array($message => $val);
		}
		
		$this->_error_messages = array_merge($this->_error_messages, $lang);
	}
	
	/**
	 * 表单验证
	 * 
	 * @return bool
	 */
	public function form_valid()
	{
		if (count($_POST) == 0) {
			return FALSE;
		}

		foreach ($this->_field_data as $field => $row) {
			if ($row['is_array'] == TRUE) {
				$this->_field_data[$field]['postdata'] = $this->_array_get($_POST, $row['keys']);
			} else {
				if (isset($_POST[$field]) && $_POST[$field] != "") {
					$this->_field_data[$field]['postdata'] = $_POST[$field];
				}
			}

			$this->_execute($row, explode('|', $row['rules']), $this->_field_data[$field]['postdata']);
		}

		// Did we end up with any errors?
		$total_errors = count($this->_error_array);

		if ($total_errors > 0) {
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data
		$this->_reset_post_array();

		// No errors, validation passes!
		if ($total_errors == 0) {
			return TRUE;
		}

		// Validation fails
		return FALSE;
	}
	
	protected function _array_get($array, $keys, $index = 0)
	{
		foreach ($keys as $segment) {
			if ( is_array($array) && array_key_exists($segment, $array)) {
				$array =& $array[$segment];
			} else {
				return FALSE;
			}
		}
		return $array;
	}
	
	protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
	{
		if (is_array($postdata)) {
			// 如果postdata是数组的话遍历验证
			foreach ($postdata as $val) {
				$this->_execute($row, $rules, $val, $cycles);
				$cycles++;
			}
			return TRUE;
		}
		
		// 遍历验证规则
		foreach ($rules as $rule) {
			$_in_array = FALSE;

			if ($row['is_array'] == TRUE && is_array($this->_field_data[$row['field']]['postdata'])) {
			
				if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles])) {
					continue;
				}

				$postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
				$_in_array = TRUE;
			} else {
				$postdata = $this->_field_data[$row['field']]['postdata'];
			}

			// 是否是回调函数
			$callback = FALSE;
			if (substr($rule, 0, 9) == 'callback_')
			{
				$rule = substr($rule, 9);
				$callback = TRUE;
			}

			// 分离方法名和参数
			$param = FALSE;
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match)) {
				$rule   = $match[1];
				$param = $match[2];
			}

			// 是否是回调验证
			if ($callback === TRUE)
			{
				if ( ! method_exists($this->_controller, $rule) ) continue;
				// 调用控制器的验证/处理回调函数
				$result = $this->_controller->$rule($postdata, $param);

				// 重新分配到主数组中
				if ($_in_array == TRUE) {
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				} else {
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}

				// If the field isn't required and we just processed a callback we'll move on...
				if ( ! in_array('required', $rules, TRUE) && $result !== FALSE) continue;
				
			} else {
				if ( ! method_exists($this, $rule) ) {
					// 如果不是该成员方法,则检查全局方法
					if (function_exists($rule)) {
						$result = $rule($postdata);

						if ($_in_array == TRUE) {
							$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
						} else {
							$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
						}
					} else {
						log_message('debug', "Unable to find validation rule: ".$rule);
					}
					continue;
				}
				
				$result = $this->$rule($postdata, $param);

				if ($_in_array == TRUE) {
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				} else {
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}
			}

			// 获取错误信息
			if ($result === FALSE)
			{
				if ( ! isset($this->_error_messages[$rule])) {
					$line = 'Unable to access an error message corresponding to your field name.';
				} else {
					$line = $this->_error_messages[$rule];
				}
				
				// 构造参数
				$args   = array($row['label'], $param);
				// 构造错误信息
				$message = _vf($line, $args);

				// 报错错误信息
				$this->_field_data[$row['field']]['error'] = $message;

				if ( ! isset($this->_error_array[$row['field']]))
				{
					$this->_error_array[$row['field']] = $message;
				}
				return TRUE;
			}
		}
	}
	
	// --------------------------------  验证方法 START ---------------------------------------

	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}

	/**
	 * Performs a Regular Expression match test.
	 *
	 * @access	public
	 * @param	string
	 * @param	regex
	 * @return	bool
	 */
	public function regex_match($str, $regex)
	{
		if ( ! preg_match($regex, $str))
		{
			return FALSE;
		}

		return  TRUE;
	}

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	public function matches($str, $field)
	{
		if ( ! isset($_POST[$field]))
		{
			return FALSE;
		}

		$field = $_POST[$field];

		return ($str !== $field) ? FALSE : TRUE;
	}

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	public function is_unique($str, $field)
	{
		list($table, $field)=explode('.', $field);
		$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
		
		return $query->num_rows() === 0;
    }

	/**
	 * 最大字符长度验证
	 *
	 * @param  string $str 要验证的字符串
	 * @param  int    $val 长度值
	 * @return	bool
	 */
	public function max_length($str, $val)
	{
		if ( ! is_int($val) ) {
			return FALSE;
		}
		$val = (int)$val;

		if ( function_exists('mb_strlen') ) {
			return ( mb_strlen($str) > $val ) ? FALSE : TRUE;
		}
		return ( strlen($str) > $val ) ? FALSE : TRUE;
	}

	/**
	 * 固定字符长度验证
	 *
	 * @param	string $str 要验证的字符串
	 * @param	int    $val 长度值
	 * @return	bool
	 */
	public function exact_length($str, $val)
	{
		if ( ! is_int($val) ) {
			return FALSE;
		}
		$val = (int)$val;
		
		if ( function_exists('mb_strlen') ) {
			return ( mb_strlen($str) != $val ) ? FALSE : TRUE;
		}

		return ( strlen($str) != $val ) ? FALSE : TRUE;
	}

	/**
	 * 验证email地址的合法性
	 *
	 * @param	string $str 要验证的字符串
	 * @return	bool
	 */
	public function valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str) ) ? FALSE : TRUE;
	}

	/**
	 * 验证email地址串的合法性
	 *
	 * @param	string $str 要验证的字符串
	 * @return	bool
	 */
	public function valid_emails($str)
	{
		if (strpos($str, ',') === FALSE) {
			return $this->valid_email(trim($str));
		}

		foreach (explode(',', $str) as $email) {
			if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function valid_ip($ip)
	{
		return $this->CI->input->valid_ip($ip);
	}

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	/**
	 * Integer
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	/**
	 * Decimal number
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	/**
	 * Greather than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function greater_than($str, $min)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str > $min;
	}

	/**
	 * Less than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function less_than($str, $max)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str < $max;
	}

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural($str)
	{
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_no_zero($str)
	{
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}

		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_base64($str)
	{
		return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
	}
	
	// --------------------------------  验证方法 END ---------------------------------------
}
?>