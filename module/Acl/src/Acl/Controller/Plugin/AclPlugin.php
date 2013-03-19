<?php
namespace Acl\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container as SessionContainer;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Authentication\AuthenticationService;

class AclPlugin extends AbstractPlugin{
	
	protected $sessionContainer;
	
	private function getSessionContainer(){
		if(!$this->sessionContainer){
			$this->sessionContainer = new SessionContainer('Zend_Auth');
		}
		return $this->sessionContainer;
	}
	
	public function doAuthorization($e){
		$acl = new Acl();
		
		$acl->addResource('book');
		$acl->addResource('user');
		$acl->addResource('application');
		
		$guest = 3;
		$user = 2;
		$admin = 1;
		
		$acl->addRole(new Role($guest));
		$acl->addRole(new Role($user), $guest);
		$acl->addRole(new Role($admin), $user);
		
		$acl->allow($guest, 'user', array('user:register', 'user:login'));
		$acl->allow($guest, 'book', array('book:index', 'book:detail'));
		$acl->allow($guest, 'application');
		
		$acl->allow($user, 'user', 'user:logout');
		$acl->deny($user, 'user', array('user:login', 'user:register'));
		
		$acl->allow($admin, 'book', array('book:new', 'book:edit', 'book:delete'));
		
		$controller = $e->getTarget();
		$controllerClass = get_class($controller);
		$moduleName = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
		$auth = new AuthenticationService();
		$role = (!$auth->getIdentity()) ? $guest : $auth->getIdentity()->role;
		$routeMatch = $e->getRouteMatch();
		
		$actionName = strtolower($routeMatch->getParam('action', 'not-found'));
		$controllerName = $routeMatch->getParam('controller', 'not-found');
		$controllerName = strtolower(array_pop(explode('\\', $controllerName)));
		
		if(!$acl->isAllowed($role, $moduleName, $controllerName.':'.$actionName)){
			$router = $e->getRouter();
			$url = '';
			if($role == 3){
				$url = $router->assemble(array(), array('name' => 'user'));
			}
			else{
				//TODO
				$url = $router->assemble(array(), array('name' => 'book'));
			}
			$response = $e->getResponse();
			$response->setStatusCode(302);
			$response->getHeaders()->addHeaderLine('Location', $url);
			$e->stopPropagation();
		}
	}
	
}