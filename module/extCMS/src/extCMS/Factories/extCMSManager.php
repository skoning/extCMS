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
  
  private function getConfig()
  {
    $config = array();
    
    $entries = $this->em->getRepository('extCMS\Entity\Config')->findAll();
    
    foreach ($entries as $entry) {
      $config[$entry->getKey()] = $entry->getValue();
    }

    $this->sl->setService('extCMSConfig', new Config($config, true));
    
    return $config;
  }
}