 <?php
abstract class GR_Controller
{
	private $output = null;
	
	public function __construct()
	{
		$module  = Router::getModule();
		$control = Router::getContrl();
		$control_file = APP_PATH.'/modules/'.$module.'/controllers/'.$control.'_ctr.php';
		
		if ( ! file_exists($control_file) ) {
            $data = array(
                'message' => $module.'::'.$control.' not found.'
            );
            return ($this->output = Loader::loadError('404', $data));
        }
		
		require $control_file;
		
		// 检查类和方法是否存在
        $class = ucfirst($module).'_'.ucfirst($control).'_Ctr';
		$method = Router::getMethod();
		
		if ( ! class_exists($class) ) {
            $data = array(
                'message' => $class.' does not exist'
            );
            return ($this->output = Loader::loadError('404', $data));
        }
		
		$class_methods = array_map('strtolower', get_class_methods($class));        
        if ( ! in_array($method, $class_methods) && ! in_array('__call', $class_methods) ) {
            $data = array(
                'message' => '"'.$method.'" not found in '.$class.'.'
            );
            return ($this->output = Loader::loadError('404', $data));
        }
		
		// create it, barf if necessary.
        try {
            $c = new $class();
        } catch(Exception $e){
            $data = array(
                'message' => $e->getMessage()
            );
            return ($this->output = Loader::loadError('general', $data));
        }
		
		// autoload helpers and such.
        $conf = Loader::loadConfig('core', 'autoload');
        if ($conf) {
            foreach ($conf as $key => $val) {
                if (!isset($val['0'])) { 
                    continue;
                }
                foreach($val as $k => $v) {
                    switch ($key) {
                        case 'helpers': Loader::loadHelper($v); break;
                        case 'plugins': Loader::LoadPlugin($v); break;
                        case 'libraries': 
                            foreach($v as $lib => $args){
                                Loader::loadLibrary($lib, $args); 
                            }
                        break;
                    }
                }
            }
        }
		
		// 执行控制器方法
		if (FALSE !== ($params = Router::getArgv())) {
			$this->output = call_user_func_array(array(&$c, $method), $params);
		} else {
			$this->output = call_user_func(array(&$c, $method));
		}
	}
	
	public function __toString()
	{
		return $this->output;
	}
}
?>