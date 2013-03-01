<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

uses('Grace_Routing_Definition');
uses('Grace_Routing_Route');

/**
 * 路由映射类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Routing
 */
class Grace_Routing_Mapper
{
	/**
     * 添加的通用路由信息
     * 
     * @var array
     */
    protected $_attach_common = NULL;

    /**
     * 添加的组路由规则数组
     * 
     * @var array
     */
    protected $_attach_routes = NULL;

    /**
     * 路由定义对象集
     * 
     * @var array
     */
    protected $_definitions = array();

    /**
     * 路由对象集
     * 
     * @var array
     */
    protected $_routes = array();

    /**
     * 记录试图匹配的路由信息
     * 
     * @var array
     */
    protected $_log = array();

    /**
     * 
     * 构造函数
     * 
     * @param array $attach 组路由规则
     * 
     */
    public function __construct($attach = array())
	{
        foreach ( $attach as $path_prefix => $rule ) {
            $this->attach($path_prefix, $rule);
        }
		
		$path = ( IS_HMVC === TRUE ? '/{:module}' : '' ) . '/*';
		// 添加默认路由
		$this->addRoute('default', $path);
    }
	
	
	/**
	 * 获取路由定义实例
	 * 
	 * @param  string $type        路由类型
	 * @param  array  $rule        路由规则
	 * @param  string $path_prefix 组路由路径前缀
	 * @return object
	 */
	public static function createDefinitionInstance($type, $rule, $path_prefix = null)
	{
		return new Grace_Routing_Definition($type, $rule, $path_prefix);
	}
	
	/**
	 * 获取路由实例
	 * 
	 * @param array $params 路由类参数
	 */
	public static function createRouteInstance($params)
	{
        return new Grace_Routing_Route($params);
	}

    /**
     * 添加一个路由定义到数组中
     * 
     * @param  string $name 路由名
     * @param  string $path 路由路径
     * @param  array  $rule 路由规则
     * @return void
     */
    public function addRoute($name, $path, $rule = array())
    {
        // 覆盖路由名及路径
        $rule['name'] = $name;
        $rule['path'] = $path;

        // 删除路由名和前缀
        unset($rule['name_prefix']);
        unset($rule['path_prefix']);

        // 在数组开头插入路由定义对象
		array_unshift($this->_definitions, self::createDefinitionInstance('single', $rule));
    }

    /**
     * 对一个有效的路径前缀增加组路由(比如模块名)
	 * 
	 * @param  string $path_prefix 路径前缀
     * @param  array  $rule        组路由规则
     * @return void
     */
    public function attach($path_prefix, $rule)
    {
		// 在数组开头插入路由定义对象
		array_unshift($this->_definitions, self::createDefinitionInstance('attach', $rule, $path_prefix));
    }

    /**
     * 取得路径匹配成功的路由
     * 
     * @param  string $path   匹配路径
     * @param  array  $server 服务器变量
     * @return object|false
     * 
     */
    public function match($path, $server = array())
    {
		// 重置日志数组
        $this->_log = array();

        // 遍历现有的路由进行匹配
        foreach ($this->_routes as $route) {
            $this->_logRoute($route);
            if ($route->isMatch($path, $server)) {
                return $route;
            }
        }
		
        // 遍历剩余的路由再匹配
        while ($this->_attach_routes || $this->_definitions) {
            $route = $this->_createNextRoute();
            $this->_logRoute($route);
            if ($route->isMatch($path, $server)) {
                return $route;
            }
        }
        return FALSE;
    }

    /**
     * 根据存在的路由规则将参数还原为一个URI地址
     * 
     * @param string $name  路由名称
     * @param array  $data  参数数组
     * @return string|false 还原成功/失败
     * 
     */
    public function generate($data = array(), $name = 'default')
    {
		// 是否存在该路由规则?
        if ( isset($this->_routes[$name]) ) {
            return $this->_routes[$name]->generate($data);
        }

       // 遍历剩余的路由再还原
        while ($this->_attach_routes || $this->_definitions) {
            $route = $this->_createNextRoute();
            if ($route->name == $name) {
                return $route->generate($data);
            }
        }
		
        return FALSE;
    }

    /**
     * 设置全部路由规则(可以读取getRoutes()生成的缓存对象)
     * 
     * @param array $routes 路由对象数组
     * @return void
     */
    public function setRoutes($routes = array())
    {
        $this->_routes = $routes;
        $this->_definitions   = array();
        $this->_attach_custom = array();
        $this->_attach_routes = array();
    }

    /**
     * 获取路由对象数组, 可以序列化保存到缓存中, 以便调用
	 * setRoutes()
	 * 
     * @return array
     */
    public function getRoutes()
    {
		// convert remaining definitions as needed
        while ($this->_attach_routes || $this->_definitions) {
            $this->_createNextRoute();
        }
        return $this->_routes;
    }

    /**
     * 获取企图匹配的路由日志
     * 
     * @return array
     */
    public function getLog()
    {
		return $this->_log;
    }

    /**
     * 添加一个企图匹配的路由到日志中
     * 
     * @param  object $route 路由对象
     * @return array
     */
    protected function _logRoute($route)
    {
		$this->_log[] = $route;
    }

    /**
     * 获取下一个要匹配的路由对象(转换路由定义为路由对象)
     * 
     * @return object
     */
    protected function _createNextRoute()
    {
		// 是否存在组路由
        if ($this->_attach_routes) {
            // 继续获取组路由的下一个定义
            $rule = $this->_getNextAttach();
        } else {
            // 获取一下独立定义
            $rule = $this->_getNextDefinition();
        }

        // 创建路由规则对象
		$route = self::createRouteInstance($rule);
		
        // 保留route对象到数组集合中
        $name = $route->name;
        if ($name) {
            $this->_routes[$name] = $route;
        } else {
            // 没有的路由名称, 则无法反差(比如 generate())
            $this->_routes[] = $route;
        }
        return $route;
    }

    /**
     * 获取下一个路由规则
     * 
     * @return array 路由规则
     */
    protected function _getNextDefinition()
    {
		// 获得下一个定义并提取定义类型
        $def =  array_shift($this->_definitions);
        $rule = $def->getRule();
        $type = $def->getType();

        // 如果是单路由定义
        if ($type == 'single') {
            return $rule;
        }

		// 如果是组路由定义, 保留组路由规则
        $this->_attach_routes = $rule['routes'];
        unset($rule['routes']);

        // 保留剩些的路由参数
        $this->_attach_common = $rule;
        
        // 重置数组指针
        reset($this->_attach_routes);
        
        // 取得组路由的下一个路由规则
        return $this->_getNextAttach();
    }

    /**
     * 获取组路由下一个路由规则
     * 
     * @return array 路由规则
     */
    protected function _getNextAttach()
    {
		$key = key($this->_attach_routes);
        $val = array_shift($this->_attach_routes);

        // which definition form are we using?
        if (is_string($key) && is_string($val)) {
            // short form, named in key
            $rule = array(
                'name' => $key,
                'path' => $val,
                'parameters' => array(
                    'action' => $key,
                ),
            );
        } elseif (is_int($key) && is_string($val)) {
            // short form, no name
            $rule = array(
                'path' => $val,
            );
        } elseif (is_string($key) && is_array($val)) {
            // long form, named in key
            $rule = $val;
            $rule['name'] = $key;
            // if no action, use key
            if (! isset($rule['parameters']['action'])) {
                $rule['parameters']['action'] = $key;
            }
        } elseif (is_int($key) && is_array($val)) {
            // long form, no name
            $rule = $val;
        } else {
            throw new Exception("Route rule for '$key' should be a string or array.");
        }

        // unset any path or name prefix on the rule itself
        unset($rule['name_prefix']);
        unset($rule['path_prefix']);

        // now merge with the attach info
        $rule = array_merge_recursive($this->attach_common, $rule);
		
        return $rule;
    }
}
?>