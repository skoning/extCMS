<?php
namespace extCMS;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use extCMS\Listener\ServiceManagerListener;
use Zend\EventManager\EventInterface;
use extCMS\Manager\AuthenticationAdapter;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface, BootstrapListenerInterface
{
  public function onBootstrap(EventInterface $e)
  {
    /** 
     * @var \Zend\ServiceManager\ServiceManager
     */
    $sm = $e->getApplication()->getServiceManager();
    $em = $sm->get('doctrine.entitymanager.orm_default');
    $dem = $em->getEventManager();
    $dem->addEventListener(array(\Doctrine\ORM\Events::postLoad), new ServiceManagerListener($sm));
    $allowOverride = $sm->getAllowOverride();
  }
  
  public function getAutoloaderConfig()
  {
    return array(
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                __NAMESPACE__ . '\twigExtensions' => 'data/twigExtensions',
                'extCMS\GoogleAuthentication' => __DIR__ . '/src/extCMS/GoogleAuthenticator' 
            )
        )
    );
  }
  
  public function getConfig()
  {
    $config = include __DIR__ . '/config/module.config.php';
    
    return $config;
  }
  
  public function getServiceConfig()
  {
    return array(
      'factories' => array(
        'extCMSAuthenticationService' => function($serviceManager) {
          return $serviceManager->get('doctrine.authenticationservice.orm_default');
        },
      ),
      'zfctwig' => array(
        'extensions' => array(
          'test2',
        )
      ),
    );
  }
}