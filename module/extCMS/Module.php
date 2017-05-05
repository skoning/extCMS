<?php
namespace extCMS;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use extCMS\Listener\ServiceManagerListener;
use Zend\EventManager\EventInterface;
use extCMS\Manager\AuthenticationAdapter;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, BootstrapListenerInterface
{
  public function onBootstrap(EventInterface $e)
  {
    /* @var $sm \Zend\ServiceManager\ServiceManager */
    $sm = $e->getApplication()->getServiceManager();
    $em = $sm->get('doctrine.entitymanager.orm_default');
    $dem = $em->getEventManager();
    $dem->addEventListener(array(\Doctrine\ORM\Events::postLoad), new ServiceManagerListener($sm));
    
    $allowOverride = $sm->getAllowOverride();
    $sm->setAllowOverride(true);
    
    // Get Twig Extensions
    $extensions = $em->getRepository('extCMS\Entity\Extension')->findAll();
    
    $zfctwig = array();
    
    foreach( $extensions as $extension ) {
      $zfctwig[$extension->getName()] = 'extCMS\twigExtensions\\' . $extension->getName();
    }
    
    $sm->setService('zfctwig', $zfctwig);
    
    $sm->setAllowOverride($allowOverride);
  }
  
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
        },
      ),
    );
  }
}