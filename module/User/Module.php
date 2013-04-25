<?php
namespace User;

use User\Model\User;
use User\Model\UserTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * Konfigurační třída modulu User.
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
    
    /**
     * Konfiguruje service manager.
     * @return multitype:multitype:NULL  |\Book\Model\BookTable|\Zend\Db\TableGateway\TableGateway|\Book\Model\FavoriteTable konfigurační pole pro ServiceManager
     */
    public function getServiceConfig(){
    	return array(
    			'factories' => array(
    					'User\Model\UserTable' => function($sm){
    						$tableGateway = $sm->get('UserTableGateway');
    						$table = new UserTable($tableGateway);
    						return $table;
    					},
    					'UserTableGateway' => function($sm){
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new User());
    						return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
    					}
    					),
    			);
    }
}
