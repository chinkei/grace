<?php
class Grace_Auth_Acl
{
	/**
	 * Rules settings for the application ACL. Defined in acl.conf.php
	 * @var array
	 */
	public $rules;

	/**
	 * Default route to be reroute to if no custome fail route is defined for a certain rule.
	 * @var string|array
	 */
	public $defaultFailedRoute = array('/error-default/failed-route/please-set-in-route', 404);

	/**
	 * Check if the user Role is in the allowed rule
	 *
	 * @param string $role Role of a user, usually retrieve from user's login session
	 * @param string $resource Resource name (use Controller class name)
	 * @param string $action Action name (use Method name)
	 * @return bool
	 */
	protected function hasAllowed($role, $resource, $action='') {
		if ($action=='') {
			return isset($this->rules[$role]['allow'][$resource]);
		} else {
			if(isset($this->rules[$role]['allow'][$resource])) {
				$actionlist = $this->rules[$role]['allow'][$resource];
				if ($actionlist==='*')
					return true;
				else
					return in_array($action, $actionlist);
			} else {
				if( isset($this->rules[$role]['allow']) && is_array($this->rules[$role]['allow']) && isset($this->rules[$role]['allow'][0]) ){
					return ($this->rules[$role]['allow'][0] == '*');
				}
				return false;
			}
		}
	}

	/**
	 * Check if the user Role is allowed to access the resource or action list or both.
	 *
	 * <code>
	 * //Check if member is allowed for BlogController->post
	 * Doo::acl()->isAllowed('member', 'BlogController', 'post' );
	 *
	 * //Check if member is allowed for BlogController
	 * Doo::acl()->isAllowed('member', 'BlogController');
	 * </code>
	 *
	 * @param string $role Role of a user, usually retrieve from user's login session
	 * @param string $resource Resource name (use Controller class name)
	 * @param string $action Action name (use Method name)
	 * @return bool
	 */
	public function isAllowed($role, $resource, $action='') {
		if (!$this->hasDenied($role, $resource, $action)) {
			if ($this->hasAllowed($role, $resource, $action)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if the user Role is denied from the resource or action list or both.
	 *
	 * @param string $role Role of a user, usually retrieve from user's login session
	 * @param string $resource Resource name (use Controller class name)
	 * @param string $action Action name (use Method name)
	 * @return bool
	 */
	protected function hasDenied($role, $resource, $action='') {
		if ($action=='') {
			return isset($this->rules[$role]['deny'][$resource]);
		} else {
			if( isset($this->rules[$role]['deny']) && $this->rules[$role]['deny']=='*'){
				$this->rules[$role]['deny'] = array('*');
			}

			if (isset($this->rules[$role]['deny'][$resource])) {
				$actionlist = $this->rules[$role]['deny'][$resource];

				if($actionlist==='*')
					return true;
				else
					return in_array($action, $actionlist);
			} else {
				return false;
			}
		}
	}

	/**
	 * Check if the user Role is denied from the resource or action list or both.
	 *
	 * <code>
	 * //Check if member is denied from BlogController->post
	 * Doo::acl()->isDenied('member', 'BlogController', 'post' );
	 *
	 * //Check if member is denied from BlogController
	 * Doo::acl()->isDenied('member', 'BlogController');
	 * </code>
	 *
	 * @param string $role Role of a user, usually retrieve from user's login session
	 * @param string $resource Resource name (use Controller class name)
	 * @param string $action Action name (use Method name)
	 * @return bool
	 */
	public function isDenied($role, $resource, $action='') {
		if ($this->hasDenied($role, $resource, $action)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the user's role is able to access the resource/action.
	 *
	 * @param string $role Role of a user, usually retrieve from user's login session
	 * @param string $resource Resource name (use Controller class name)
	 * @param string $action Action name (use Method name)
	 * @return array|string Returns the fail route if user cannot access the resource.
	 */
	public function process($role, $resource, $action='') {
		if ($this->isDenied($role, $resource, $action) ) {
			//echo 'In deny list';

			if (isset($this->rules[$role]['failRoute'])) {
				$route = $this->rules[$role]['failRoute'];

				if (is_string($route)) {
					return array($route, 'internal');
				} else {
					if (isset($route[$resource])) {
						return (is_string($route[$resource]))? array($route[$resource], 'internal') : $route[$resource] ;
					} elseif (isset( $route[$resource.'/'.$action] )) {
						$rs = $route[$resource.'/'.$action];
						return (is_string($rs))? array($rs, 'internal') : $rs;
					} elseif (isset( $route['_default'] )) {
						return (is_string($route['_default']))? array($route['_default'], 'internal') : $route['_default'];
					}
				}
			}
			return $this->defaultFailedRoute;

		} else if($this->isAllowed($role, $resource, $action)==false) {
			//echo 'Not in allow list<br>';

			if (isset($this->rules[$role]['failRoute'])) {
				$route = $this->rules[$role]['failRoute'];

				if (is_string($route)) {
					return array($route, 'internal');
				} else {
					if (isset($route[$resource])) {
						return (is_string($route[$resource]))? array($route[$resource], 'internal') : $route[$resource] ;
					} elseif (isset( $route[$resource.'/'.$action] )) {
						$rs = $route[$resource.'/'.$action];
						return (is_string($rs))? array($rs, 'internal') : $rs;
					} elseif (isset( $route['_default'] )) {
						return (is_string($route['_default']))? array($route['_default'], 'internal') : $route['_default'];
					}
				}
			}
			return $this->defaultFailedRoute;
		}
	}
}
?>