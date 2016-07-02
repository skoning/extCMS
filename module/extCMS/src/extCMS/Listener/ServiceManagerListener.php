<?php
namespace extCMS\Listener;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class ServiceManagerListener
{
  protected $sm;
  
  public function __construct(ServiceManager $sm)
  {
    $this->sm = $sm;
  }
  
  public function postLoad($eventArgs)
  {
    $entity = $eventArgs->getEntity();
    
    if( $entity instanceof ServiceLocatorAwareInterface ) {
      $entity->setServiceLocator( $this->sm);
    }
  }
}