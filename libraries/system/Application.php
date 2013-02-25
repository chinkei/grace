<?php
class Application
{
	private static $_isRun = false;
	
	public static function run()
	{
		if (self::$_isRun === TRUE) {
			return;
		}
		// 载入对象注册类
		import_file('core.Register');
		import_file('core.Controller');
		
		self::hook()->addHookListener('EVENT_APP_RUN');
		self::hook()->addHookListener('EVENT_APP_END');

		self::output()->startBuffer();
		//self::output()->sendHeader('X-Powered-By: ' . FRAMEWORK_NAME);

		self::hook()->trigger('EVENT_APP_RUN');
		
		import_file('core.Router');
		// 初始化路由
		$router = new Router();
		$router->init();
		self::hmvc_init();
		
		//if(null !== self::conf()->CONTENT_TYPE){
		//	self::output()->addHeader('Content-Type: '.self::conf()->CONTENT_TYPE);
		//}

		self::output()->dump();

		self::hook()->trigger('EVENT_APP_END');
		
		self::$_isRun = TRUE;
	}
	
	/**
	 * HMVC初始化
	 */
	private static function hmvc_init()
	{
		self::loadCoreFile('controller');
		self::loadCoreFile('model');
		self::loadCoreFile('view');
		
		// 载入模块的Hook文件
		self::hook()->loadModuleHook();
		
		$module = Router::getModule();
		$contrl = Router::getContrl();
		
		$ctrFile = APP_PATH . '/modules/' . $module . '/controllers/' . $contrl . '_ctr.php';
		
		if (!file_exists($ctrFile)) {
            $data = array(
                'message' => $module.'::'.$contrl.' not found.'
            );
			// 404
			exit($data['message']); 
            //return ($this->output = Loader::loadError('404', $data));
        }
        // require it.
        require($ctrFile);
		
		$class = ucfirst($module).'_'.ucfirst($contrl).'_Ctr';
		$method = Router::getMethod();
		
		if (!class_exists($class)) {
            $data = array(
                'message' => $class.' does not exist'
            );
            // 404
			exit($data['message']); 
        }
        
        $cls_methods = array_map('strtolower', get_class_methods($class));        
        if ( ! in_array($method, $cls_methods) && ! in_array('__call', $cls_methods)) {
            $data = array(
                'message' => '"'.$method.'" not found in '.$class.'.'
            );
            // 404
			exit($data['message']); 
        }
		
		// TODO自动载入
		
		// create it, barf if necessary.
        try {
            $cls = new $class();
        } catch(Exception $e){
            $data = array(
                'message' => $e->getMessage()
            );
            // 404
			exit($data['message']);
        }
		
		$params = Router::getArgv();
		if (FALSE !== $params) {
			call_user_func_array(array(&$cls, $method), $params);
		} else {
			$cls->$method();
		}
	}
	
	public static function config()
	{
		return self::getCoreInstance('config');
	}
	
	public static function output()
	{
		return self::getCoreInstance('output');
	}
	
	public static function security()
	{
		return self::getCoreInstance('security');
	}
	
	public static function request()
	{
		return self::getCoreInstance('request');
	}
	
	public static function validator()
	{
		return self::getCoreInstance('validator');
	}
	
	public static function acl()
	{
		return self::getCoreInstance('acl');
	}
	
	public static function hook()
	{
		return self::getCoreInstance('hook');
	}
	
	public static function layout()
	{
		return self::getCoreInstance('layout');
	}
	
	public static function load()
	{
		
	}
	
	/**
	 * 载入核心类实例并注册
	 * 
	 * @param  string $name 核心文件名
	 * @return string 对象实例
	 */
	private static function getCoreInstance($name);
	{
		if ( !Register::isExists($name) ) {
			$className = loadCoreFile($name);
			if (!class_exists($className)) {
				trigger_error('class: "'.$className.'" is not exists.', E_USER_ERROR);
			}
			Register::set($name, new $className());
		}
		return Register::get($name);
	}
	
	/**
	 * 载入核心文件(可扩展)
	 * 
	 * @param  string $name 核心文件名
	 * @return string 类名
	 */
	private static function loadCoreFile($name)
	{
		if (FALSE === import_file('core.'.$name)) {
			trigger_error('file: "core/'.$name.'" is not exist', E_USER_ERROR);
		}
		
		$app_core_class = APP_CORE_PREFIX . ucfirst($name);
		if (FALSE !== import_file('core.'.$app_core_class)) {
			return $app_core_class;
		}
		return 'GR_'.ucfirst($name);
	}
	
	public static function db()
	{
		import_file('core.Db');
		if ( !Register::isExists('db') ) {
			Register::set('db', new Db());
		}
		return Register::get('db');
	}
	
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