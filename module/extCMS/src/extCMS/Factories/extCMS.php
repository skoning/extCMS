<?php
namespace extCMS\Factories;

use Doctrine\ORM\EntityManager;
use Zend\Config\Config;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class extCMS implements FactoryInterface
{
  public $em;
  
  public $sm;
  
  public $config;
  
  public function createService(ServiceLocatorInterface $serviceLocator)
  {
    $this->setServiceLocator($serviceLocator);
    $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    
    /**
     * @var Translator $translator
     */
    $this->config = new Config($this->getConfig());
    $translator = $this->getServiceLocator()->get('translator');
    $translator->setLocale($this->config->get('locale', 'en_US'));
    
    return $this;
  }
  
  private function getConfig()
  {
    return $this->getServiceLocator()->get('extCMSConfig')->toArray();
  }
  
  /**
   * Get configuration value
   *
   * @param $key ConfigKey to get value from
   * @param $default The default value if the configKey is missing
   */
  public function get( $key, $default = null )
  {
    return $this->config->get($key, $default);
  }
  
  public function setServiceLocator( ServiceLocatorInterface $sm )
  {
    $this->sm = $sm;
  }
  
  public function getServiceLocator( )
  {
    return $this->sm;
  }
  
  public function setEntityManager( EntityManager $em = null )
  {
    $this->em = is_null($em) ? $this->getServiceLocator()->get('doctrine.entitymanager.orm_default') : $em;
  }
  
  public function getEntityManager( )
  {
    if( is_null( $this->em ) ) {
      $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
    
    return $this->em;
  }

}