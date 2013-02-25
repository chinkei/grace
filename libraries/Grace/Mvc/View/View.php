<?php
class Grace_Mvc_View_View
{
	protected $_view;
	
	protected $_script_inlines = array();
	
	protected $_scripts = array();
	protected $_styles  = array();
	
	public function __construct(Grace_Mvc_View_Interface $view)
	{
		$this->_view = $view;
	}
	
	public function setViewDir($path)
	{
		$this->_view->setViewDir($path);
	}
	
	public function assign($tpl_var, $value = '')
	{
		$this->_view->assign($tpl_var, $value);
	}
	
	public function fetch($filename, $cache_id = '')
	{
		return $this->_view->fetch($filename, $cache_id);
	}
	
	public function display($filename, $cache_id = '')
	{
		$this->_view->assign('_sys_script', $this->getScript());
		$this->_view->assign('_sys_stryle', $this->getStyle());
		$this->_view->assign('_sys_script_inline', $this->getScriptInline());
		
		$this->_view->display($filename, $cache_id);
	}
	
	public function getScript()
	{
		$js = '';
		foreach ($this->_scripts as $js) {
			$js .= '<script src="'.$js.'" type="text/javascript"></script>'."\n";
		}
		return $js;
	}
	
	public function getStyle()
	{
		$css = '';
		foreach ($this->_styles as $css) {
			$css .= '<link href="'.$css.'" rel="stylesheet" type="text/css" />'."\n";
		}
		return $css;
	}
	
	public function getScriptInline()
	{
		$inline = '';
		
		if ( !empty($this->_script_inlines) ) {
			$inline = '<script type="text/javascript">'."\n";
			foreach($this->_script_inlines as $line){
                $inline .= $line."\n";
            }
			$inline .= '</script>'."\n";
		}
		
		return $inline;
	}
	
	public function addScript($scripts)
	{
		if (is_array($scripts)) {
            foreach ($scripts as $val) {
				if ($val != '') {
					$this->_scripts[] = $val;
				}
            }
        } else {
            if ($scripts != '') {
                $this->_scripts[] = $scripts;
            }
        }
	}
	
	public function addScriptInline($lines)
	{
		if (is_array($lines)) {
            foreach ($lines as $val) {
				if ($val != '') {
					$this->_script_inlines[] = $val;
				}
            }
        } else {
            if ($lines != '') {
                $this->_script_inlines[] = $lines;
            }
        }
	}
	
	public function addStyle($styles = '')
	{
		if (is_array($styles)) {
            foreach ($styles as $val) {
				if ($val != '') {
					$this->_styles[] = $val;
				}
            }
        } else {
            if ($styles != '') {
                $this->_styles[] = $styles;
            }
        }
	}
}
?>