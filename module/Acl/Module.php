<?php
namespace Acl;

use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Konfigurační třída modulu ACL.
 * @author Magda Kutišová
 *
 */
class Module
{
	/**
	 * Nastavuje cestu ke konfiguraci modulu.
	 */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    /**
     * Funkce probíhající při spuštění události bootstrap.
     * @param MvcEvent $e MVC událost
     */
    public function onBootstrap(MvcEvent $e){
    	$eventManager = $e->getApplication()->getEventManager();
    	$eventManager->attach('route', array($this, 'loadConfiguration'), 2);
    }
    
    /**
     * Načte konfiguraci pro ACL.
     * @param MvcEvent $e MVC událost
     */
    public function loadConfiguration(MvcEvent $e){
    	$application = $e->getApplication();
    	$sm = $application->getServiceManager();
    	$sharedManager = $application->getEventManager()->getSharedManager();
    	
    	$router = $sm->get('router');
    	$request = $sm->get('request');
    	
    	$matchedRoute = $router->match($request);
    	if (null !== $matchedRoute){
    		$sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch',
    				function ($e) use ($sm){
    					$sm->get('ControllerPluginManager')->get('AclPlugin')
    						->doAuthorization($e);
    				}, 2
    				);
    	}
    }

    /**
     * Konfiguruje autoloader.
     * @return multitype:multitype:string  multitype:multitype:string pole autoloaderů
     */
    public function getAutoloaderConfig()
    {
        return array(
        	'Zend\Loader\ClassMapAutoloader' => array(
        			__DIR__ . '/autoload_classmap.php',
        			),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
