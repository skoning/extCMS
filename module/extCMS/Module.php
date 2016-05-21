<?php
namespace extCMS;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface
{
  
  public function getAutoloaderConfig()
  {
    return array(
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                __NAMESPACE__ . '\twigExtensions' => 'data/twigExtensions',
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
        }
      )
    );
  }
}