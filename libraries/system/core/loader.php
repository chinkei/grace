<?php
class GR_Loader
{
	/**
	 * 载入控制器
	 * 
	 * @param  string $module  模块名
	 * @param  string $control 控制器
	 * @param  array  $args    参数
	 * @return boolean|object
	 */
	public static function loadController($module, $control, $args = array())
	{
		$class = ucfirst(strtolower($module)).'_'.ucfirst(strtolower($control)).'_Ctr';
        if (class_exists($class)) {
            return new $class($args);
        }
        
        $file = APP_PATH.DS.'modules'.DS.strtolower($module).DS.'controllers'.DS.strtolower($controller).'_ctr.php'; 
        if (file_exists($file) && self::loadFile($file)) {
            return new $class($args);
        }
        trigger_error($module.'_'.$control.' could not be found/loaded.', E_USER_ERROR);
		return FALSE;
	}
	
	/**
	 * 载入Model
	 * 
	 * @param  string $module  模块名
	 * @param  string $model   Model名
	 * @param  array  $args    参数
	 * @return boolean|object
	 */
	public static function loadModel($module, $model, $args = array())
	{
		static $_loaded = array();
		
		$model_name = ucfirst(strtolower($module)).'_'.ucfirst(strtolower($model)).'_Mdl';
		
		if (isset($_loaded[$model_name])) {
			return $_loaded[$model_name];
		}
		
		$file = APP_PATH.DS.'modules'.DS.strtolower($module).DS.'models'.DS.strtolower($model).'._mdl.php';
		if (file_exists($file) && self::loadFile($path)) {
			try {
				$_loaded[$model_name] = new $model_name($args);
			} catch(Exception $e) {
				trigger_error('Failed to load model: '.$model_name.' - '.$e->getMessage(), E_USER_ERROR);
				return FALSE;
			}
			return $_loaded[$model_name];
		}
		trigger_error($module.'_'.$model.' could not be found/loaded.', E_USER_ERROR);
		return FALSE;
	}
	
	/**
	 * 载入视图
	 * 
	 * @param  string $module  模块名
	 * @param  string $control 视图名
	 * @param  array  $data    数据
	 * @return boolean|object
	 */
	public static function loadView($module, $view, $data = array())
	{
		$file = APP_PATH.DS.'layouts'.DS.'views'.DS.strtolower($module).DS.strtolower($view).'_view.php';
        if (file_exists($file)) {
            return self::getOutput($file, $data);
        }
        
        $file = APP_PATH.DS.'modules'.DS.strtolower($module).DS.'views'.DS.strtolower($view).'_view.php';
        if (file_exists($file)) {
            return self::getOutput($file, $data);
        }
        
        trigger_error('View: "'.$view.'" could not be found.', E_USER_WARNING);
        return FALSE;
	}
	
	/**
	 * 载入布局
	 * 
	 * @param  array  $data   页面数据
	 * @param  string $layout 布局名
	 * @return boolean|string 
	 */
	public static function loadLayout($data = array(), $layout = 'default')
	{
		$file = APP_PATH.DS.'layouts'.DS.strtolower($layout).'._layout.php';
        if (file_exists($file)) {
            return self::getOutput($file, $data);
        }
        
        trigger_error('Layout: "'.$layout.'" could not be found.', E_USER_ERROR);
        return FALSE;
	}
	
	/**
	 * 载入类库
	 * 
	 * @param  array  $data   页面数据
	 * @param  string $layout 布局名
	 * @return boolean|string 
	 */
	public static function loadLibrary($library, $args = array())
	{
		static $_loaded = array();
		
		if (isset($_loaded[$library])) {
			return $_loaded[$library];
		}
		
		$file = APP_PATH.DS.'libraries'.DS.$library.'.php';
        if (file_exists($file) && self::loadFile($file)) {
            $_loaded[$library] = new $library($args);
            return $_loaded[$library];
        }
        return new stdClass();
	}
	
	public static function loadConfig($file = NULL, $item = NULL)
	{
		static $config = array();
		
		if (isset($config[$file])) {
			return (isset($config[$file][$item]) ? $config[$file][$item] : $config[$file] );
		}
		
		if (is_null($file)) {
			return $config;
		}
		
		if ( file_exists(APP_PATH.DS.'config'.DS.$file.'.php') ) {
			include(APP_PATH.DS.'config'.DS.$file.'.php');
			if (is_array($$file)) {
				$config[$file] = $$file;
				if ( is_null($item) ) {
					return $config[$file];
				}
				if ( !isset($config[$file][$item]) ) {
					trigger_error($item.'could not be found in '.$file, E_USER_ERROR);
					return false;
				}
				return $config[$file][$item];
				
			}
		} else {
			trigger_error('file: '.$file.'.php could not be found ', E_USER_ERROR);
			return false;
		}
	}
	
	
	public static function loadFile($file, $isRequire = TRUE)
	{
		static $_loadFile = array();
	
		if (isset($_loadFile[$file])) {
            return $_loadFile[$file];
        }
    
        $_loadFile[$file] = ($isRequire ? require($file) : include($file));

        return $_loadFile[$file];
	}
	
	public static function loadError($type = 'general', $data = array())
	{
		if (is_numeric($type)) {
            switch($type) {
                case '401': $msg = '401 Access Denied'; break;
                case '404': $msg = '404 Not Found'; break;
                case '500': $msg = '500 Internal Server Error'; break;
            }
            if (isset($msg)) {
                header('HTTP/1.1 '.$msg);
                header('Status: '.$msg);
            }
        }
        $content = self::loadView('error', $type, $data);
        $data = array(
            'type' => $type,
            'type_msg' => $msg,
            'content' => $content,
            'data' => $data
        );
        return self::loadLayout($data, 'error');
	}
	
	public static function getOutput($file, $data = array())
	{
		if(!file_exists($file)) {
            return NULL;
        }   
        
        ob_start();
        if (is_array($data)) {
            extract($data);
        }
        include($file);
        $strRet = ob_get_contents();
        ob_end_clean();
        
        return $strRet;
	}
	
	public static function loadPlugin($plugin)
	{
		
	}
}
?>