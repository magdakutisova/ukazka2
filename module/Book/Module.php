<?php
namespace Book;

use Book\Model\FavoriteTable;
use Book\Model\Book;
use Book\Model\BookTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * Konfigurační třída modulu Book.
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
        			__DIR__ . '/autoload_classmap.php'
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
    					'Book\Model\BookTable' => function($sm){
    						$tableGateway = $sm->get('BookTableGateway');
    						$table = new BookTable($tableGateway);
    						$cacheAdapter = $sm->get('Zend\Cache\Storage\Filesystem');
    						$table->setCache($cacheAdapter);
    						return $table;
    					},
    					'BookTableGateway' => function($sm){
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new HydratingResultSet();
    						$resultSetPrototype->setObjectPrototype(new Book());
    						return new TableGateway('book', $dbAdapter, null, $resultSetPrototype);
    					},
    					'Book\Model\FavoriteTable' => function($sm){
    						$tableGateway = $sm->get('FavoriteTableGateway');
    						$table = new FavoriteTable($tableGateway);
    						return $table;
    					},
    					'FavoriteTableGateway' => function($sm){
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						return new TableGateway('favorite', $dbAdapter);
    					}
    					),
    			);
    }
    
}
