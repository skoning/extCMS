<?php
namespace extCMS\Factories;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use extCMS\Exceptions\ManagerException;
use Doctrine\ORM\EntityManager;

class ManagerControllerFactory extends AbstractActionController implements FactoryInterface
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
  
  protected $msg;
  
  protected $isLoggedIn = false;
  
  protected final function loginRedirect() {
    if(!$this->isLoggedIn)
      $this->redirect()->toUrl($this->url()->fromRoute('manager/login') . '?redirect_url=' . $this->getServiceLocator()->get('request')->getUri()->getPath());
  }
  
  public final function getEntityManager()
  {
    if( null === $this->em ) {
      $this->em = $this->sl->get('doctrine.entitymanager.orm_default');
    }
  
    return $this->em;
  }
  
  public final function createService(ServiceLocatorInterface $serviceLocator)
  {
    $serviceLocator = $serviceLocator->getServiceLocator();
    $this->setServiceLocator($serviceLocator);
    $this->checkLogin();
    $this->sl->get('extCMSManager');
    $this->em = $this->sl->get('doctrine.entitymanager.orm_default');
    return $this;
  }
  
  public final function setServiceLocator($serviceLocator)
  {
    if( null !== $serviceLocator ) {
      $this->sl = $serviceLocator;
    } else {
      throw new ManagerException('No Service Locator given..');
    }
  }
  
  public final function getServiceLocator() {
    return $this->sl;
  }
  
  protected function checkLogin()
  {
    $sl = $this->getServiceLocator();
    $auth = $sl->get('extCMSAuthenticationService');

    if( !$auth->hasIdentity() ) {
      $this->isLoggedIn = false;
    } else {
      $this->isLoggedIn = true;
    }
  }
}
