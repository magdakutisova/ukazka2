<?php
namespace Book;

use Book\Model\FavoriteTable;

use Book\Model\Book;
use Book\Model\BookTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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
