<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * 路由定义规则类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Router
 * 
 */
class Grace_Routing_Definition
{
    /**
     * 
     * 路由类型 'single' or 'attach' 单个或组路由
     * 
     * @var string
     * 
     */
    protected $_type;
    
    /**
     * 
     * 规则格式
     * 
     * @var array|callable
     * 
     */
    protected $_rule;
    
    /**
     * 
     * 组路由类型的，路由路径前缀
     * 
     * @var string
     * 
     */
    protected $_path_prefix;
    
    /**
     * 
     * 构造函数.
     * 
     * @param string              $type 路由类型
     * 
     * @param array|callable      $spec 定义的路由规则
     * 
     * @param string $path_prefix 组路由'attach'类型的路由路径前缀
     * 
     */
    public function __construct($type, $rule, $path_prefix = NULL)
    {
        $this->_type        = $type;
        $this->_rule        = $rule;
        $this->_path_prefix = $path_prefix;
    }
    
    /**
     * 
     * 获取定义的路由类型
     * 
     * @return string
     * 
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * 
     * 获取路由规则(如果是组路由'attach'则增加路由路径前缀参数)
     * 
     * @return array
     * 
     */
    public function getRule()
    {
        if (is_callable($this->_rule)) {
            $this->_rule = call_user_func($this->_rule);
        }
        
        if ($this->_type == 'attach') {
            $this->_rule['path_prefix'] = $this->_path_prefix;
        }
        
        return $this->_rule;
    }
}
?>