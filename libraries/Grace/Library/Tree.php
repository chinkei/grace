<?php
if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/



//---------------------------------------DEBUG START-----------------------------------

//$list_array = array(
//			array('city_id'=>1, 'pid'=>0, 'city_name'=>'北京'),
//			array('city_id'=>2, 'pid'=>0, 'city_name'=>'上海'),
//			array('city_id'=>3, 'pid'=>0, 'city_name'=>'深圳'),
//			array('city_id'=>4, 'pid'=>1, 'city_name'=>'海淀区'),
//			array('city_id'=>6, 'pid'=>5, 'city_name'=>'浦东区'),
//			array('city_id'=>5, 'pid'=>2, 'city_name'=>'浦东区'),
//		);
//		
//$Tree = new Grace_Library_Tree($list_array);
//
//print_r($Tree->getChildsTree(0));
//print_r($Tree->getChilds(2));
//print_r($Tree->getNodeLever(6));
//
//$category = $Tree->getChilds(2);
//
////遍历输出
//foreach ($category as $key=>$id)
//{
//	echo $id.$Tree->getLayer($id, '|-').$Tree->getValue($id)."\n";
//}

//--------------------------------------DEBUG END---------------------------------------

/**
 * 树形结构处理类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Library
 */
class Grace_Library_Tree
{
	/**
	 * key:节点ID, value:节点名
	 * 
	 * @var array
	 */
	protected $_name = array();
	
	/**
	 * key:节点ID, value:父类树ID
	 * 
	 * @var array
	 */
	protected $_parent_id = array();
	
	/**
	 * key:节点ID, value:其他数据
	 * 
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * 构造函数
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->_init();
	}
	
	/**
	 * 根据二维数组设置节点
	 * 例：array(array('id'=>1, 'pid' => 0, 'name'=> 'Tree1'), array('id'=>1, 'pid' => 0, 'name'=> 'Tree1'))
	 * 
	 * @param  array $arr 二维数组
	 * @return bool
	 */
	public function setNodesByArr($arr)
	{
		$this->_init();
		
		if (is_array($arr)) {
			foreach ($arr as $val) {
				$values = array_values($val);
				
				$data   = array();
				if ( count($values) > 3 ) {
					$data = array_slice($values, 3);
				}
				$this->_setNode($values[0], $values[1], $values[2], $data)
			}
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 获取节点树
	 * 
	 * @param int $id
	 */
	public function getChildsTree($id = 0)
	{
		$childs = array();
		
		foreach ($this->_parent_id as $child => $pid) {
			if ($pid == $id) {
				$childs[$child] = $this->getChildsTree($child);
			}
		}
		return $childs;
	}
	
	/**
	 * 获取所有的子节点
	 *
	 * @param  int   $id
	 * @return array
	 */
	public function getAllChilds($id = 0)
	{
		$childArray = array();
		$childs     = $this->getChilds($id);
		
		foreach ($childs as $child) {
			$childArray[] = $child;
			$childArray = array_merge($childArray, $this->getAllChilds($child));
		}
		return $childArray;
	}
	
	/**
	 * 获取下级子节点
	 *
	 * @param  int   $id
	 * @return array
	 */
	public function getChilds($id)
	{
		$childs = array();
		
		foreach ($this->_parent_id as $child => $pid) {
			if ($pid == $id) {
				$childs[] = $child;
			}
		}
		return $childs;
	}
	
	/**
	 * 获取所有父节点
	 * 
	 * @param  int   $id 节点Id
	 * @return array
	 */
	public function getParents($id)
	{
		$parents = array();
		
		if ( isset($this->_parent_id[$id]) ) {
			$pid = $this->_parent_id[$id];
			$parents[] = $pid;
			
			$parents = array_merge($parents, $this->getParents($pid));
		}
		return $parents;
	}
	
	/**
	 * 获取层级前缀
	 * 
	 * @param  int    $id     节点Id
	 * @param  string $prefix 前缀
	 * @return string 
	 */
	public function getLayer($id, $prefix = '|-')
	{
		return str_repeat($prefix, count($this->getParents($id)));
	}
	
	/**
	 * 根据ID获取节点名
	 * 
	 * @param  int $id 节点Id
	 * @return string|bool
	 */
	public function getName($id)
	{
		return isset($this->_name[$id]) ? $this->_name[$id] : FALSE;
	}
	
	/**
	 * 获取其他节点数据
	 * 
	 * @param  int $id 节点Id
	 * @return string|bool
	 */
	public function getData($id)
	{
		return isset($this->_data[$id]) ? $this->_data[$id] : FALSE;
	}
	
	/**
	 * 初始化数据
	 * 
	 * @return void
	 */
	private function _init()
	{
		$this->_name      = array();
		$this->_data      = array();
		$this->_parent_id = array();
	}
	
	/**
	 * 设置节点数据
	 * 
	 * @param  int    $id   节点Id
	 * @param  int    $pid  父Id
	 * @param  string $name 节点名
	 * @param  array  $data 其他节点数据
	 * @return void
	 */
	private function _setNode($id, $pid, $name, $data = array())
	{
		$pid = ( $pid ? $pid : 0 );
		
		$this->_name[$id]      = $name;
		$this->_parent_id[$id] = $pid;
		$this->_data[$id]      = $data;
	}
}
?>