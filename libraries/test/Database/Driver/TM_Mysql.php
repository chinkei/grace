<?php
<?php   
//---------------------------------------------------------------------   
//      MySQL Master/Slave数据库读写操作类   
//   
// 开发作者: heiyeluren   
// 版本历史:    
//          2006-09-20  基本单数据库操作功能, 25 个接口   
//          2007-07-30  支持单Master/多Slave数据库操作，29个接口   
//          2008-09-07  修正了上一版本的部分Bug   
//          2009-11-17  在Master/Slave类的基础上增加了强化单主机操作，   
//                      增加了部分简洁操作接口和调试接口，优化了部分代码，   
//                      本版本共42个接口
//			2009-11-26	增加了部分调试和性能监控接口
//			2009-12-13  整合到TMPHP项目中
//			2009-12-19  修改mysql_free_result判断bug
//			2009-12-20  增加 getEscapeString(),delete(),exec()等函数，修改部分bug
// 应用项目: Y!NCP, Y!SNS, TM MiniSite   
// 功能描述：自动支持Master/Slave 读/写 分离操作，支持多Slave主机   
//   
//-----------------------------------------------------------------------   
       
  
  
       
/**  
 * DB Common class  
 *  
 * 描述：能够分别处理一台Master写操作，多台Slave读操作  
 */  
class TM_DB_Mysql
{   
    /**  
     * 数据库配置信息  
     */  
    var $wdbConf = array();   
    var $rdbConf = array();   
    /**  
     * Master数据库连接  
     */  
    var $wdbConn = null;   
    /**  
     * Slave数据库连接  
     */  
    var $rdbConn = array();   
    /**  
     * 当前操作的数据库链接  
     */  
    var $currConn = null;   
    /**  
     * 是否只有一台Master数据库服务器  
     */  
    var $singleHost = true;   
    /**  
     * 数据库结果  
     */  
    var $dbResult;   
    /**  
     * 数据库查询结果集  
     */  
    var $dbRecord;   
  
    /**  
     * SQL语句  
     */  
    var $dbSql;   
    /**  
     * 数据库编码  
     */  
    var $dbCharset = "UTF8";   
    /**  
     * 数据库版本  
     */  
    var $dbVersion = "";   
  
  
    /**  
     * 初始化的时候是否要连接到数据库  
     */  
    var $isInitConn = false;   
    /**  
     * 是否要设置字符集  
     */  
    var $isCharset = false;   
    /**  
     * 数据库结果集提取方式  
     */  
    var $fetchMode = MYSQL_ASSOC;   
    /**  
     * 执行中发生错误是否记录日志  
     */  
    var $isLog = false;   
    /**  
     * 执行中的SQL是否记录，设定级别  
     *  
     * 0:不记录     
     * 1:记录insert    
     * 2:记录insert/update    
     * 3:记录insert/update/delete    
     * 4:记录select/insert/update/delete  
     */  
    var $logSqlLevel = 0;   
    /**  
     * 记录Log文件路径  
     */  
    var $logFile = '/tmp/db_mysql_error.log';   
    /**  
     * 是否查询出错的时候终止脚本执行  
     */  
    var $isExit = false;   
    /**  
     * MySQL执行是否出错了  
     */  
    var $isError = false;   
    /**  
     * MySQL执行错误消息  
     */  
    var $errMsg  = '';   
    /**  
     * 是否记录SQL运行时间  
     */  
    var $isRuntime = true;   
    /**  
     * SQL执行时间  
     */  
    var $runTime = 0;   
  
  
  
  
    //------------------------   
    //   
    //  类本身操作方法   
    //   
    //------------------------   
  
    /**  
     * 设置类属性  
     *  
     * @param str $key  需要设置的属性名  
     * @param str $value 需要设置的属性值  
     * @return void  
     */  
    function set($key, $value){   
        $this->$key = $value;   
    }   
  
    /**  
     * 读取类属性  
     *  
     * @param str $key  需要读取的属性名  
     * @return void  
     */  
    function get($key){   
        return $this->$key;   
    }   
  
  
  
  
    //------------------------   
    //   
    //   基础底层操作接口   
    //   
    //------------------------   
  
    /**  
     * 构造函数  
     *   
     * 传递配置信息，配置信息数组结构：  
     * $masterConf = array(  
     *        "host"    => Master数据库主机地址  
     *        "user"    => 登录用户名  
     *        "pwd"    => 登录密码  
     *        "db"    => 默认连接的数据库  
     *    );  
     * $slaveConf = array(  
     *        "host"    => Slave1数据库主机地址|Slave2数据库主机地址|...  
     *        "user"    => 登录用户名  
     *        "pwd"    => 登录密码  
     *        "db"    => 默认连接的数据库  
     *    );  
     *  
     * @param bool $singleHost  是否只有一台主机  
     * @return void  
     */  
    function TM_DB_Mysql($masterConf, $slaveConf=array(), $singleHost = true){   
        //构造数据库配置信息   
        if (is_array($masterConf) && !empty($masterConf)){   
            $this->wdbConf = $masterConf;   
        }   
        if (!is_array($slaveConf) || empty($slaveConf)){   
            $this->rdbConf = $masterConf;   
        } else {   
            $this->rdbConf = $slaveConf;   
        }   
        $this->singleHost = $singleHost;   
        //初始化连接（一般不推荐）   
        if ($this->isInitConn){   
            $this->getDbWriteConn();   
            if (!$this->singleHost){   
                $this->getDbReadConn();   
            }   
        }   
    }   
  
    /**  
     * 获取Master的写数据连接  
     */  
    function getDbWriteConn(){   
        //判断是否已经连接   
        if ($this->wdbConn && is_resource($this->wdbConn)) {   
            return $this->wdbConn;   
        }   
        //没有连接则自行处理   
        $db = $this->connect($this->wdbConf['host'], $this->wdbConf['user'], $this->wdbConf['pwd'], $this->wdbConf['db']);   
        if (!$db || !is_resource($db)) {   
            return false;   
        }   
        $this->wdbConn = $db;   
        return $this->wdbConn;   
    } 
  
    /**  
     * 连接到MySQL数据库公共方法  
     */  
    protected function connDb($dbHost, $dbUser, $dbPasswd, $dbDatabase)
	{ 
    }   
  
    /**  
     * 关闭数据库连接  
     */  
    protected function close($dbConn=null, $closeAll=false)
	{
	
    }   
  
    /**  
     * 选择数据库  
     */  
    function selectDb($dbName, $dbConn=null)
	{   
        
    }   
  
    /**  
     * 执行SQL语句（底层操作）  
     */  
    public function _query($sql, $isMaster=false)
	{   
    }
	
	public function execute()
	{
		
	}
	
	public function resultArray()
	{
		
	}
	
	public function rowArray()
	{
		
	}
	
	public function colArray()
	{
		
	}
	
	public function rowColOne()
	{
		
	}
	
	public function insert()
	{
		
	}
	
	public function update()
	{
		
	}
	
	public function delete()
	{
		
	}
	
	public function getLastId()
	{
		
	}
	
	public function getLastSql()
	{
		
	}
	
	public function getEscapeString()
	{
		
	}
  
  
  
    //--------------------------   
    //   
    //       数据获取接口   
    //   
    //--------------------------   
    /**  
     * 获取SQL执行的全部结果集(二维数组)  
     *  
     * @param string $sql 需要执行查询的SQL语句  
     * @return 成功返回查询结果的二维数组,失败返回false, 数据空返回NULL  
     */  
    public function getAll($sql, $isMaster=false){   
        if (!$this->_query($sql, $isMaster)){   
            return false;   
        }   
        $this->dbRecord = array();   
        while ($row = @mysql_fetch_array($this->dbResult, $this->fetchMode)) {   
            $this->dbRecord[] = $row;   
        }
		if (is_resource($this->dbResult)){
			@mysql_free_result($this->dbResult);   
		}
        if (!is_array($this->dbRecord) || empty($this->dbRecord)){   
            return NULL;   
        }   
        return $this->dbRecord;   
    }   
  
    /**  
     * 获取单行记录(一维数组)  
     *  
     * @param string $sql 需要执行查询的SQL语句  
     * @return 成功返回结果记录的一维数组,失败返回false, 数据空返回NULL  
     */  
    function getRow($sql, $isMaster=false){   
        if (!$this->_query($sql, $isMaster)){   
            return false;   
        }   
        $this->dbRecord = array();   
        $this->dbRecord = @mysql_fetch_array($this->dbResult, $this->fetchMode);   
		if (is_resource($this->dbResult)){
			@mysql_free_result($this->dbResult);   
		} 
        if (!is_array($this->dbRecord) || empty($this->dbRecord)){   
            return NULL;   
        }   
        return $this->dbRecord;   
    }   
  
    /**  
     * 获取一列数据(一维数组)  
     *  
     * @param string $sql 需要获取的字符串  
     * @param string $field 需要获取的列,如果不指定,默认是第一列  
     * @return 成功返回提取的结果记录的一维数组,失败返回false, 数据空返回NULL  
     */  
    function getCol($sql, $field='', $isMaster=false){   
        if (!$this->_query($sql, $isMaster)){   
            return false;   
        }   
        $this->dbRecord = array();   
        while($row = @mysql_fetch_array($this->dbResult, $this->fetchMode)){   
            if (trim($field) == ''){   
                $this->dbRecord[] = current($row);   
            } else {   
                $this->dbRecord[] = $row[$field];   
            }   
        }   
		if (is_resource($this->dbResult)){
			@mysql_free_result($this->dbResult);   
		} 
        if (!is_array($this->dbRecord) || empty($this->dbRecord)){   
            return NULL;   
        }   
        return $this->dbRecord;   
    }   
  
    /**  
     * 获取一个数据(当条数组)  
     *  
     * @param string $sql 需要执行查询的SQL  
     * @return 成功返回获取的一个数据,失败返回false, 数据空返回NULL  
     */  
    function getOne($sql, $field='', $isMaster=false){   
        if (!$this->_query($sql, $isMaster)){   
            return false;   
        }   
        $this->dbRecord = array();   
        $row = @mysql_fetch_array($this->dbResult, $this->fetchMode);   
		if (is_resource($this->dbResult)){
			@mysql_free_result($this->dbResult);   
		} 
        if (!is_array($row) || empty($row)){   
            return NULL;   
        }   
        if (trim($field) != ''){   
            $this->dbRecord = $row[$field];   
        }else{   
            $this->dbRecord = current($row);   
        }   
        return $this->dbRecord;   
    }   
  
  
  
    /**  
     * 获取指定各种条件的记录  
     *  
     * @param string $table 表名(访问的数据表)  
     * @param string $field 字段(要获取的字段)  
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)  
     * @param string $order 排序(按照什么字段排序,不包括ORDER BY,默认为空)  
     * @param string $limit 限制记录(需要提取多少记录,不包括LIMIT,默认为空)  
     * @param bool $single 是否只是取单条记录(是调用getRow还是getAll,默认是false,即调用getAll)  
     * @return 成功返回记录结果集的数组,失败返回false  
     */  
    function getRecord($table, $field='*', $where='', $order='', $limit='', $single=false, $isMaster=false){   
        $sql = "SELECT $field FROM $table";   
        $sql .= trim($where)!='' ? " WHERE $where " : $where;   
        $sql .= trim($order)!='' ? " ORDER BY $order " : $order;   
        $sql .= trim($limit)!='' ? " LIMIT $limit " : $limit;   
        if ($single){   
            return $this->getRow($sql, $isMaster);   
        }   
        return $this->getAll($sql, $isMaster);   
    }   
  
    /**  
     * 获取指点各种条件的记录(跟getRecored类似)  
     *  
     * @param string $table 表名(访问的数据表)  
     * @param string $field 字段(要获取的字段)  
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)  
     * @param array $order_arr 排序数组(格式类似于: array('id'=>true), 那么就是按照ID为顺序排序, array('id'=>false), 就是按照ID逆序排序)  
     * @param array $limit_arr 提取数据的限制数组()  
     * @return unknown  
     */  
    function getRecordByWhere($table, $field='*', $where='', $arrOrder=array(), $arrLimit=array(), $isMaster=false){   
        $sql = " SELECT $field FROM $table ";   
        $sql .= trim($where)!='' ? " WHERE $where " : $where;   
        if (is_array($arrOrder) && !empty($arrOrder)){   
            $arrKey = key($arrOrder);   
            $sql .= " ORDER BY $arrKey " . ($arrOrder[$arrKey] ? "ASC" : "DESC");   
        }   
        if (is_array($arrLimit) && !empty($arrLimit)){   
            $startPos = intval(array_shift($arrLimit));   
            $offset = intval(array_shift($arrLimit));   
            $sql .= " LIMIT $startPos,$offset ";   
        }   
        return $this->getAll($sql, $isMaster);   
    }   
  
    /**  
     * 获取指定条数的记录  
     *  
     * @param string $table 表名  
     * @param int $startPos 开始记录  
     * @param int $offset 偏移量  
     * @param string $field 字段名  
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)  
     * @param string $order 排序(按照什么字段排序,不包括ORDER BY,默认为空)  
     * @return 成功返回包含记录的二维数组,失败返回false  
     */  
    function getRecordByLimit($table, $startPos, $offset, $field='*', $where='', $oder='', $isMaster=false){   
        $sql = " SELECT $field FROM $table ";   
        $sql .= trim($where)!='' ? " WHERE $where " : $where;   
        $sql .= trim($order)!='' ? " ORDER BY $order " : $order;   
        $sql .= " LIMIT $startPos,$offset ";   
        return $this->getAll($sql, $isMaster);   
    }   
  
    /**  
     * 获取排序记录  
     *  
     * @param string $table 表名  
     * @param string $orderField 需要排序的字段(比如id)  
     * @param string $orderMethod 排序的方式(1为顺序, 2为逆序, 默认是1)  
     * @param string $field 需要提取的字段(默认是*,就是所有字段)  
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)  
     * @param string $limit 限制记录(需要提取多少记录,不包括LIMIT,默认为空)  
     * @return 成功返回记录的二维数组,失败返回false  
     */  
    function getRecordByOrder($table, $orderField, $orderMethod=1, $field='*', $where='', $limit='', $isMaster=false){   
        //$order_method的值为1则为顺序, $order_method值为2则2则是逆序排列   
        $sql = " SELECT $field FROM $table ";   
        $sql .= trim($where)!='' ? " WHERE $where " : $where;   
        $sql .= " ORDER BY $orderField " . ( $orderMethod==1 ? "ASC" : "DESC");   
        $sql .= trim($limit)!='' ? " LIMIT $limit " : $limit;   
        return $this->getAll($sql, $isMaster);   
    }   
  
    /**  
     * 分页查询(限制查询的记录条数)  
     *  
     * @param string $sql 需要查询的SQL语句  
     * @param int $startPos 开始记录的条数  
     * @param int $offset 每次的偏移量,需要获取多少条  
     * @return 成功返回获取结果记录的二维数组,失败返回false  
     */  
    function limit_query($sql, $startPos, $offset, $isMaster=false){   
        $start_pos = intval($startPos);   
        $offset = intval($offset);   
        $sql = $sql . " LIMIT $startPos,$offset ";   
        return $this->getAll($sql, $isMaster);   
    }   
  
  
    //--------------------------   
    //   
    //     无数据返回操作接口   
    //   
    //--------------------------   
    /**  
     * 执行执行非Select查询操作  
     *  
     * @param string $sql 查询SQL语句  
     * @return bool  成功返回SQL影响的数据函数，失败返回false
     */  
    public function execute($sql){   
        if (!$this->_query($sql, true)){   
            return false;   
        }
	    return @mysql_affected_rows($this->currConn); 
    }    
  
    /**  
     * 自动执行操作(针对Insert/Update操作)  
     *  
     * @param string $table 表名  
     * @param array $field_array 字段数组(数组中的键相当于字段名,数组值相当于值, 类似 array( 'id' => 100, 'user' => 'heiyeluren')  
     * @param int $mode 执行操作的模式 (是插入还是更新操作, 1是插入操作Insert, 2是更新操作Update)  
     * @param string $where 如果是更新操作,可以添加WHERE的条件  
     * @return mixed 执行成功返回影响的行数, 失败返回false  
     */  
    function autoExecute($table, $arrField, $mode, $where='', $isMaster=false){   
        if ($table=='' || !is_array($arrField) || empty($arrField)){   
            return false;   
        }   
        //$mode为1是插入操作(Insert), $mode为2是更新操作   
        if ($mode == 1){   
            $sql = " INSERT INTO `$table` SET ";   
        } elseif ($mode == 2) {   
            $sql = " UPDATE `$table` SET ";   
        } else {   
            $this->errorLog("Operate type '$mode' is error, in call DB::autoExecute process table $table.");   
            return false;   
        }   
        foreach ($arrField as $key => $value){   
            $sql .= "`$key`='$value',";   
        }   
        $sql = rtrim($sql, ',');   
        if ($mode==2 && $where!=''){   
            $sql .= "WHERE $where";   
        }   
        return $this->execute($sql);   
    }
    
    /**  
     * 获取某个表的Count  
     *   
     * @param array $arrField 需要处理的where条件的key，value  
     * @param string $table 需要获取的表名  
     * @return 成功返回获取的一个整数值,失败返回false, 数据空返回NULL  
     */  
    function getCount($arrField, $notFields, $table){   
        $sql = "SELECT COUNT(1) as cnt FROM ".$table." WHERE ";   
        foreach ($arrField as $key => $value)    {   
            $sql .= " `$key`='$value' AND ";   
        }   
        if (!empty($notFields)) {   
            foreach ($arrField as $key => $value)    {   
                $sql .= " `$key`!='$value' AND ";   
            }   
        }   
        $sql .= " 1 ";   
        $row = $this->getOne($sql);   
        if ($row===NULL || $row===false){   
            return $row;   
        }   
        if (is_array($row)){   
            return (int)current($row);   
        }   
        return (int)$row;   
    }   
          
  
    /**  
     * 锁表表  
     *  
     * @param string $tblName 需要锁定表的名称  
     * @return mixed 成功返回执行结果，失败返回错误对象  
     */  
    function lockTable($tblName){   
        return $this->_query("LOCK TABLES $tblName", true);   
    }   
  
    /**  
     * 对锁定表进行解锁  
     *  
     * @param string $tblName 需要锁定表的名称  
     * @return mixed 成功返回执行结果，失败返回错误对象  
     */  
    function unlockTable($tblName){   
        return $this->_query("UNLOCK TABLES $tblName", true);   
    }   
  
    /**  
     * 设置自动提交模块的方式（针对InnoDB存储引擎）  
     * 一般如果是不需要使用事务模式，建议自动提交为1，这样能够提高InnoDB存储引擎的执行效率，如果是事务模式，那么就使用自动提交为0  
     *  
     * @param bool $autoCommit 如果是true则是自动提交，每次输入SQL之后都自动执行，缺省为false  
     * @return mixed 成功返回true，失败返回错误对象  
     */  
    function setAutoCommit($autoCommit = false){   
        $autoCommit = ( $autoCommit ? 1 : 0 );   
        return $this->_query("SET AUTOCOMMIT = $autoCommit", true);   
    }   
  
    /**  
     * 开始一个事务过程（针对InnoDB引擎，兼容使用 BEGIN 和 START TRANSACTION）  
     *  
     * @return mixed 成功返回true，失败返回错误对象  
     */  
    function startTransaction(){   
        if (!$this->_query("BEGIN")){   
            return $this->_query("START TRANSACTION", true);   
        }   
    }   
  
    /**  
     * 提交一个事务（针对InnoDB存储引擎）  
     *  
     * @return mixed 成功返回true，失败返回错误对象  
     */  
    function commit(){   
        if (!$this->_query("COMMIT", true)){   
            return false;   
        }   
        return $this->setAutoCommit( true );   
    }   
  
    /**  
     * 发生错误，会滚一个事务（针对InnoDB存储引擎）  
     *  
     * @return mixed 成功返回true，失败返回错误对象  
     */  
  
    function rollback(){   
        if (!$this->_query("ROLLBACK", true)){   
            return false;   
        }   
        return $this->setAutoCommit( true );   
    }   
  

      
    //--------------------------------   
    //   
    //    数据库操作简洁接口  
    //   
    //-------------------------------- 
    
    /**  
     * 执行执行非Select查询操作  
     *  
     * @param string $sql 查询SQL语句  
     * @return bool  成功返回SQL影响的数据函数，失败返回false
     */ 
    public function exec($sql){
		return $this->execute($sql);
    }
  
    /**  
     * 查询结果：二维数组返回  
     *   
     * @param string $sql 需要执行的SQL  
     * @return mixed 如果是select操作成功返回二维数组，失败返回false，数据空返回NULL；
     * 			     如果是update/delete/insert操作，成功返回影响的行数，失败返回false
     */  
    function query($sql){   
        $optType = trim(strtolower(substr(ltrim($sql), 0, 6)));   
        if (in_array($optType, array('update', 'insert', 'delete'))){   
            return $this->execute($sql);   
        }   
        return $this->getAll($sql);   
    }   
  
    /**  
     * 插入数据  
     *  
     * @param array $field_array 字段数组(数组中的键相当于字段名,数组值相当于值, 类似 array( 'id' => 100, 'user' => 'heiyeluren')  
     * @param string $table 表名  
     * @return mixed 执行成功返回影响的行数, 失败返回false  
     */  
    function insert($arrField, $table){   
        return $this->autoExecute($table, $arrField, 1);   
    }   
  
    /**  
     * 更新数据  
     *  
     * @param array $field_array 字段数组(数组中的键相当于字段名,数组值相当于值, 类似 array( 'id' => 100, 'user' => 'heiyeluren')  
     * @param string $table 表名  
     * @param string $where 如果是更新操作,可以添加WHERE的条件  
     * @return mixed 执行成功返回影响的行数, 失败返回false  
     */  
    function update($arrField, $where, $table){   
        if (trim($where) == ''){   
            return false;   
        }   
        return $this->autoExecute($table, $arrField, 2, $where);   
    }
    
    /**
     * 删除数据 (操作危险，谨慎使用)
     *
     * @param mixed $where 	需要Where的数据，如果是一个字符串则直接拼接为 WHERE 条件后面的字符串
     * 						如果是一个数组，则传递一个一维数组，传递where条件的 字段 => 值 的方式
     * @param string $table 需要删除数据的表名
     * @return mixed 执行成功返回影响的行数, 失败返回false  
     */
    function delete($where, $table){
    	if (empty($where)){
    		return false;
    	}
        $sql = "DELETE FROM ".$table." WHERE ";
        if (is_string($where)){
        	$sql .= $where;
        } elseif (is_array($where)){
	        foreach ($where as $key => $value)    {   
	            $sql .= " `$key`='$value' AND ";   
	        }
	        $sql .= " 1 ";	        
        } else {
        	return false;
        }
		return $this->execute($sql);   	
    }
  
    
    /**  
     * 获取某个表的Count  
     *   
     * @param array $arrField 需要处理的where条件的key，value  
     * @param string $table 需要获取的表名  
     * @return 成功返回获取的一个整数值,失败返回false, 数据空返回NULL  
     */  
    public function count($arrField, $notFields, $table){ 
    	return $this->getCount($arrField, $notFields, $table);
    }
    
  
  
    //--------------------------   
    //   
    //    其他数据操作接口   
    //   
    //--------------------------   
  
    /**  
     * 获取上次插入操作的的ID  
     *  
     * @return int 如果没有连接或者查询失败,返回0, 成功返回ID  
     */  
    function getLastId(){   
        $dbConn = $this->getDbWriteConn();   
        if (($lastId = mysql_insert_id($dbConn)) > 0){   
            return $lastId;   
        }   
        return $this->getOne("SELECT LAST_INSERT_ID()", '', true);   
    }   
  
    /**  
     * 获取记录集里面的记录条数 (用于Select操作)  
     *  
     * @return int 如果上一次无结果集或者记录结果集为空,返回0, 否则返回结果集数量  
     */  
    function getNumRows($res=null){   
        if (!$res || !is_resource($res)){   
            $res = $this->dbResult;   
        }   
        return mysql_num_rows($res);   
    }   
  
    /**  
     * 获取受到影响的记录数量 (用于Update/Delete/Insert操作)  
     *  
     * @return int 如果没有连接或者影响记录为空, 否则返回影响的行数量  
     */  
    function getAffectedRows(){   
        $dbConn = $this->getDbWriteConn();   
        if ( ($affetedRows = mysql_affected_rows($dbConn)) <= 0){   
            return $affetedRows;   
        }   
        return $this->getOne("SELECT ROW_COUNT()", "", true);           
    }   
  
  
  
  
    //--------------------------   
    //   
    //    相应配合操作接口   
    //   
    //--------------------------   
  
    /**  
     * 获取最后一次查询的SQL语句  
     *  
     * @return string 返回最后一次查询的SQL语句  
     */  
    function getLastSql(){   
        return $this->dbSql;   
    }   
  
    /**  
     * 返回SQL最后操作的数据库记录结果集  
     *  
     * @return mixed 最后结果集，可能是数组或者普通单个元素值  
     */  
    function getDBRecord(){   
        return $this->dbRecord;   
    }   
  
    /**  
     * 获取当前操作的数据库连接资源  
     *  
     * @return resouce 返回当前正在执行操作的数据库链接资源  
     */  
    function getCurrConnection(){   
        return $this->currConn;   
    }   
  
    /**  
     * SQL 执行是否出错  
     *   
     * @return bool   
     */  
    function isError(){   
        return $this->isError;   
    }   
  
    /**  
     * SQL 执行错误消息  
     *   
     * @return string  
     */  
    function getError(){   
        return $this->errMsg;   
    }   
  
    /**  
     * 获取执行时间  
     *  
     * @return float  
     */  
    function getRunTime(){   
        if ($this->isRuntime){   
            return sprintf("%.6f sec",$this->runTime);   
        }   
        return 'NULL';   
    }   
  
  
    /**  
     * 获取当前时间函数  
     *  
     * @param void  
     * @return float $time  
     */  
    function getTime(){   
        list($usec, $sec) = explode(" ", microtime());   
        return ((float)$usec + (float)$sec);   
    }   


	/**
	 * 获取 real_escape_string 的字符串
	 *
	 * @param string $str 需要过滤处理的字符串
	 * @return string
	 */
	function getEscapeString($str){
		if (get_magic_quotes_gpc()){
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($this->currConn, $str);
	}
}
?>