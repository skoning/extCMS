<?php
namespace extCMS\Factories;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractControllerFactory extends AbstractActionController implements FactoryInterface
{
  
  /**
   *
   * @var ServiceLocatorInterface
   */
  protected $sl;
  
  /**
   *
   * @var EntityManager
   */
  protected $em;
  
  
  public final function createService(ServiceLocatorInterface $serviceLocator)
  {
    $serviceLocator = $serviceLocator->getServiceLocator();
    $this->setServiceLocator($serviceLocator);
    //$this->checkLogin();
    $this->sl->get('extCMS');
    $this->em = $this->sl->get('doctrine.entitymanager.orm_default');
    return $this;
  }
  
  public final function getServiceLocator() {
    return $this->sl;
  }
  
  public function setServiceLocator($serviceLocator)
  {
    if( null !== $serviceLocator ) {
      $this->sl = $serviceLocator;
    }
    return $this;
  }
}
