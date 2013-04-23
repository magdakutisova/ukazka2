<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\I18n\Translator\Translator;
use Zend\Validator\AbstractValidator;

/**
 * Konfigurační třída modulu Application.
 * @author Zend Tool
 *
 */
class Module
{
	/**
	 * Funkce probíhající při spuštění události bootstrap.
	 * @param MvcEvent $e
	 * @author Magda Kutišová
	 */
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $translator = new Translator();
        $translator->addTranslationFile(
        		'phpArray',
        		'vendor/zendframework/zendframework/zendframework-zf2-3e8cab7/resources/languages/cs/Zend_Validate.php',
        		'default',
        		'cs_CZ'
        		);
        AbstractValidator::setDefaultTranslator($translator);
    }

    /**
     * Nastavuje cestu ke konfiguraci modulu.
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Konfiguruje autoloader.
     * @return multitype:multitype:multitype:string pole autoloaderů
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
