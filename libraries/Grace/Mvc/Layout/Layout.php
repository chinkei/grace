<?php
uses('Grace_Ioc_Ioc');

class Grace_Mvc_Layout_Layout
{
	
	/**
	 * 视图对象数组
	 * 
	 * @var array
	 */
	private $_views   = array();
	
	/**
	 * 布局对象数组
	 * 
	 * @var array
	 */
	private $_layouts = array();
	
	/**
	 * 布局组件渲染
	 * 
	 * @param  string $name      布局名
	 * @param  string $component 组件名
	 * @param  string $engine    解析方式
	 * @return void
	 */
	public function render($name, $component, $engine = 'simple', $args = NULL)
	{
		if ( !isset($this->_views[$name]) ) {
			$this->_views[$name][$component] = Grace_Ioc_Ioc::resolve('load')->view($engine);
			$this->_views[$name][$component]->setViewDir(APP_PATH . '/_layout/');
		}
		
		if ( !isset($this->_layouts[$name]) ) {
			$this->_layouts[$name] = $this->_loadLayout($name);
		}
		
		if ( is_array($args) && !empty($args) ) {
			$result = call_user_func_array(array($this->_layouts[$name], $component), (array)$args);
		} else {
			$result = call_user_func(array($this->_layouts[$name], $component));
		}
		
		$this->_views[$name][$component]->assign($result);
		
		if ($engine == 'simple') {
			$file = $name . '/' . $component . '.php';
		} else {
			$file = $name . '/' . $component . '.lay';
		}
		$this->_views[$name][$component]->display($file);
	}
	
	/**
	 * 取得布局组件渲染结果
	 * 
	 * @param  string $name      布局名
	 * @param  string $component 组件名
	 * @param  string $module    模块名
	 * @param  array  $args      附件参数
	 * @return string
	 */
	public function result($name, $component, $engine = 'simple', $args = NULL)
	{
		Grace_Ioc_Ioc::resolve('output')->stopBuffer();
		
		ob_start();
		
		$this->render($name, $component, $engine);
		$content = ob_get_contents();
		ob_end_clean();
		
		Grace_Ioc_Ioc::resolve('output')->startBuffer();
		return $content;
	}
	
	/**
	 * 内部载入layout
	 * 
	 * @param  string $name   布局名称
	 * @param  string $module 模块名
	 * @return object
	 */
	private function _loadLayout($name)
	{
		return Grace_Ioc_Ioc::resolve('load')->layout($name);
	}
}
?>