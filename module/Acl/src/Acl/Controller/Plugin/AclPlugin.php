<?php
namespace Acl\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container as SessionContainer;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceManager;
use Acl\Library\AclDefinition as MyAcl;

class AclPlugin extends AbstractPlugin{
	
	public function doAuthorization($e){
		$acl = new MyAcl();
		
		$controller = $e->getTarget();
		$controllerClass = get_class($controller);
		$moduleName = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
		$auth = new AuthenticationService();
		$role = (!$auth->getIdentity()) ? 3 : $auth->getIdentity()->role;
		$routeMatch = $e->getRouteMatch();
		
		$actionName = strtolower($routeMatch->getParam('action', 'not-found'));
		$controllerName = $routeMatch->getParam('controller', 'not-found');
		$controllerNameParts = explode('\\', $controllerName);
		$controllerName = array_pop($controllerNameParts);
		$controllerName = strtolower($controllerName);
		
		if(!$acl->isAllowed($role, $moduleName, $controllerName.':'.$actionName)){
			$router = $e->getRouter();
			$url = '';
			if($role == 3){
				$url = $router->assemble(array(), array('name' => 'user'));
			}
			else{
				$url = $router->assemble(array(), array('name' => 'acl'));
			}
			$response = $e->getResponse();
			$response->setStatusCode(302);
			$response->getHeaders()->addHeaderLine('Location', $url);
			$e->stopPropagation();
		}
	}
	
}