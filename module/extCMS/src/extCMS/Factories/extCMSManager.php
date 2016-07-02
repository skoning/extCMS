<?php
namespace extCMS\Factories;

use Zend\ServiceManager\FactoryInterface;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\Translator;

class extCMSManager implements FactoryInterface
{
  public $em;
  
  public $sl;
  
  public $config;
  
  public function createService(ServiceLocatorInterface $serviceLocator)
  {
    $this->em = $serviceLocator->get('doctrine.entitymanager.orm_default');
    $this->sl = $serviceLocator;
    
    /**
     * @var Translator $translator
     */
    $this->config = new Config($this->getConfig());
    $translator = $this->sl->get('translator');
    $translator->setLocale($this->config->get('manager_locale', 'en_US'));
    
    return $this;
  }
  
  public function get( $key, $default = null )
  {
    return $this->config->get($key, $default);
  }
  
  private function getConfig()
  {
    return $this->sl->get('extCMSConfig')->toArray();
  }
}