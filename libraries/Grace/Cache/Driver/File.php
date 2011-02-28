<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 文件缓存类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Cache
 */
class Grace_Cache_Driver_File extends Grace_Cache_Cache
{
	/**
	 * 文件类型
	 * 
	 * @var string
	 */
	protected $_data_group = 'data';
	
	/**
	 * 缓存路径
	 * 
	 * @var string
	 */
	protected $_cache_path = '/tmp/';
	
	/**
	 * 默认缓存生命周期
	 * 
	 * @var int
	 */
	protected $_default_expires = 3600;
	
	/**
	 * 构造函数
	 * 
	 * @param array $sections 配置项
	 */
	public function __construct($settings)
	{
		if ( isset($settings['prefix']) ) {
			$this->_key_prefix = $settings['prefix'];
		}
		
		if ( isset($settings['cache_path']) ) {
			$this->_cache_path = rtrim($settings['cache_path'], '/') . '/cache/';
		}
		
		// 载入文件/文件夹助手
		get_instance()->load->helper('File');
		
		// 创建缓存目录
		dir_create($this->_cache_path);
		
		if ( !is_writable($this->_cache_path) ) {
			// TODO
			trigger_error($this->_cache_path."目录不可写,请手动设定可写模式!", E_USER_ERROR);
		}
	}
	
	/**
	 * 保存缓存内容
	 * 
	 * @param  string $key 键值
	 * @param  mixed  $val 非资源类型值
	 * @return bool
	 */
	public function set($key, $val, $expires = NULL)
	{
		$file = $this->_file($key);
		$data = serialize($val);
		if ( $expires === NULL ) {
			$expires = $this->_default_expires;
		}
		
		if ($this->_data_group != 'html') {
			$data = '<?php if (!defined("APP_PATH")) exit("Access Denied!"); ?>' . $data;
		}
		
		// 写入文件并设置文件的最后修改时间为有效时间
		$ret = file_put_contents($file, $data, LOCK_EX);
		touch($file, time() + $expires);
		
		return (bool)$ret;
	}
	
	/**
	 * 根据键值获取缓存值
	 * 
	 * @param  string $key 键值
	 * @return bool
	 */
	public function get($key)
	{
		$file = $this->_file($key);
		
		if ( is_file($file) ) {
			if(time() <= filemtime($file)) {
				$content = preg_replace('/(<\?php[^?>]*\?>)/i', '', file_get_contents($file));
				$content = trim($content);
				return unserialize($content);
			} else {
				unlink($file);
				return FALSE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * 删除缓存
	 * 
	 * @param  string $key 键值
	 * @return bool
	 */
	public function rm($key, $time = 0)
	{
		$file = $this->_file($key, $this->type);
		return @unlink($file);
	}
	
	/**
	 * 根据组类型删除缓存
	 * 
	 * @param string $type 要删除的缓存类型,为空时删除所有缓存
	 */
	public function clearByGroup($group = '')
	{
		$path = $this->_cache_path;
		if ($group != '') {
			$path = rtrim($this->dir, '/').'/'.$group;
		}
		
		// 遍历删除文件夹下所有文件
		dir_delete($path);
		
		// 清除文件状态缓存
		clearstatcache();
		return true;
	}
	
	/**
	 * 设置缓存组名
	 * 
	 * @param  string $group 组名
	 * @return object 
	 */
	public function setDataGroup($group = '')
	{
		$this->_data_group = $group;
		return $this;
	}
	
	/**
	 * 获取缓存文件地址
	 * 
	 * @param  string $key 缓存键值
	 * @return string  
	 */
	protected function _file($key)
	{
		$postfix = '.php';
		
		if ( $this->_data_group ) {
			// 如果缓存是html数据, 则变更文件后缀为html
			$this->_data_group == 'html' && $postfix = '.html';
			$dir = rtrim($this->_cache_path, '/') . '/' . $this->_data_group;
			!is_dir($dir) && @mkdir($dir);
			
			return $dir.'/'.$this->_data_group.'_'.$this->_getKey($key).$postfix;
		}
		
		return rtrim($this->_cache_path, '/').'/cache_' . $this->_getKey($key) . $postfix;
	}
}
?>