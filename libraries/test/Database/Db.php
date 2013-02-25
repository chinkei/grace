<?php
abstract class Grace_Database_Db
{
	const PATTERN_DEFAULT = 'WR';
	const PATTERN_MASTER  = 'W+';
	const PATTERN_SLAVE   = 'R+';
	
	public $_conn_link    = array();
	protected $_db_settings  = array();
	
	private static $_instance  = array();
	
	abstract public function connect($dbConf);
	abstract public function disconnect();
	abstract public function execute();
	abstract public function resultArray();
	abstract public function rowArray();
	abstract public function colArray();
	abstract public function rowColOne();
	abstract public function insert($table, $data = array());
	abstract public function update($table, $data = array(), $where = NULL);
	abstract public function delete($table, $where = NULL);
	abstract public function getInsertId();
	abstract public function getLastSql();
	abstract public function free();
	abstract public function escape($str);
	abstract public function getVersion();
	
	/**
	 * 获取数据库驱动对象
	 * 
	 * @param string $active_group 配置项索引
	 * @reutn object
	 */
	public static function getInstance($param = '')
	{

		include 'config.php';
		
		if ( ! isset($db) || count($db) == 0) {
			show_error('No database connection settings were found in the database config file.');
		}
		
		if ($param != '') {
			$active_group = $param;
		}
		
		if ( !isset(self::$_instance[$active_group]) ) {
			if ( !isset($db[$active_group]) ) {
				// TODO
				trigger_error('不存在这个数据模型');
			}
			include 'Driver/Mysql.php';
			//import_file('libraries.database.connectors.driver.conn_'.$db[$active_group]['dirver']);
			$dirverClass = 'Grace_Database_Driver_' . $db[$active_group]['driver'];
			// 实例化对象
			self::$_instance[$active_group] = new $dirverClass($db[$active_group]);
		}
		return self::$_instance[$active_group];
	}
	
	/**
	 * 是否是主从模式
	 * 
	 * return bool
	 */
	public function isMasterSlave()
	{
		return isset($this->_db_settings['master_slave']) ? $this->_db_settings['master_slave'] : FALSE;
	}
	
	/**
	 * 从库是否使用权重
	 * 
	 * return bool
	 */
	public function isUseWeight()
	{
		return isset($this->_db_settings['use_weight']) ? $this->_db_settings['use_weight'] : FALSE;
	}
	
	/**
	 * 获取数据编码类型
	 * 
	 * retrun mixed string|false
	 */
	public function getCharset()
	{
		return isset($this->_db_settings['char_set']) ? $this->_db_settings['char_set'] : FALSE;
	}
	
	/**
	 * 获取数据库连接句柄
	 * 
	 * @param string $mode WR|R+|W+
	 */
	public function getConnLink($mode = self::PATTERN_DEFAULT)
	{
		// 未开启主从模式或是写模式都默认是主库连接
		if ($this->isMasterSlave() === FALSE || $mode == self::PATTERN_MASTER ) {
			$mode = self::PATTERN_DEFAULT;
		}
		
		if ( ! isset($this->_conn_link[$mode]) || ( ! is_resource($this->_conn_link[$mode]) && ! is_object($this->_conn_link[$mode]) ) ) {
			$link = FALSE;
			switch ($mode) {
				case self::PATTERN_SLAVE:
					$link = $this->_getSlaveLink($this->_db_settings[self::PATTERN_SLAVE]);
				break;
				
				case self::PATTERN_MASTER:
				case self::PATTERN_DEFAULT:
					$link = $this->connect($this->_db_settings[self::PATTERN_DEFAULT]);
				break;
			}
			
			if (FALSE === $link) {
				exit('数据库连接错误！');
			}
			$this->_conn_link[$mode] = $link;
		}
		return $this->_conn_link[$mode];
	}
	
	/**
	 * 获取从库连接句柄
	 */
	protected function _getSlaveLink($settings)
	{
		if (empty($settings)) {
			// 从库都连接失败
			return FALSE;
		}
			
		$slave = $this->_getSlaveConfig($settings);
		if ( FALSE === ($conn = $this->connect($slave['config'])) ) {
			unset($settings[$slave['index']]);
			return $this->_getSlaveLink($settings);
		}
		return $conn;
	}
	
	/**
	 * 获取从库配置
	 */
	protected function _getSlaveConfig($settings)
	{
		$result = array();
		$count = count($settings);
		
		// 未启用权重功能
		if ($this->isUseWeight() === FALSE) {
			$index = mt_rand(0, $count - 1);
			$result['index']  = $index;
			$result['config'] = $settings[$index];
		} else {
			$weight = -1;
			foreach($settings as &$val) {
				$val['start'] = $weight + 1;
				$weight += $val['weight'];
				$val['end'] = $weight;
			}
			
			$rand = mt_rand(0, $weight);
			
			for ($i = 0; $i < $count; $i++) {
				if ($rand >= $settings[$i]['start'] && $rand <= $settings[$i]['end']) {
					$result['index']  = $i;
					$result['config'] = $settings[$i];
					break;
				}
			}
		}
		return $result;
		
	}
}
?>