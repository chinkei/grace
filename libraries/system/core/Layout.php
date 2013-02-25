<?php
class GR_Layout
{
	private $_views;
	
	private $_layouts;
	
	/**
	 * 呈现布局组件
	 *
	 * @param String $name      布局名称
	 * @param String $component 组件名称
	 * @param Array  $args      附加参数
	 */
	public function render($name, $component, $args = NULL)
	{
		if (!isset($this->_views[$name])) {
			$this->_views[$name] = app::loadView('_layout/' . $name . '/' . $component);
		}
		
		if (!isset($this->_layouts[$name])) {
			$this->_layouts[$name] = $this->_loadLayout($name);
		}
		
		if (NULL !== $args) {
			$result = call_user_func_array(array($this->_layouts[$name],$component), $args);
		} else {
			$result = call_user_func_array(array($this->_layouts[$name],$component));
		}
		
		$this->_views[$name]->render($result);
	}
	
	public function result($name, $component, $args = NULL)
	{
		app::output()->pauseBuffer();
		ob_start();
		$this->render($name,$component,$args);
		$content = ob_get_contents();
		ob_end_clean();
		app::output()->startBuffer();
		return $content;
	}
	
	/**
	 * 內部載入layout
	 * @param string $name 佈局名稱
	 * @return Object
	 */
	private function _loadLayout($name){
		list($name, $path) = getClassPath($name, app::conf()->LAYOUT_PATH,'Layout');
		app::load($name , $path, false,'layout');
		return new $name;
	}
}
?>