<?php
namespace extCMS\Factories;

use Zend\ServiceManager\FactoryInterface;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\Translator;
use Zend\View\Model\ViewModel;
use extCMS\Manager\Forms\LoginForm;
use extCMS\Manager\AuthenticationAdapter;
use Zend\Http\Request;
use Doctrine\ORM\EntityManager;

class extCMSManager implements FactoryInterface
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
    $translator->setLocale($this->config->get('manager_locale', 'en_US'));
    
    return $this;
  }
  
  public function get( $key, $default = null )
  {
    return $this->config->get($key, $default);
  }
  
  private function getConfig()
  {
    return $this->getServiceLocator()->get('extCMSConfig')->toArray();
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
  
  public function login( Request $request, $controller )
  {
    /**
     * @var AuthenticationService $authService
     */
    $authService = $this->getServiceLocator()->get('extCMSAuthenticationService');
    $adapter = $authService->getAdapter();
  
    $view = new ViewModel();
    $childView = new ViewModel();
    $childView->setTemplate('ext-cms/manager/login');
    $view->setTemplate('managerLayout');
  
    $form = new LoginForm('login');
    $form->setAttribute('action', $controller->url('manager/login'));
  
    $childView->setVariable('form', $form);
  
    $referer = $request->getQuery('redirect_url');
  
    $form->get('redirect_url')->setValue($referer);
  
    if ($request->isPost()) {
      $data = $request->getPost();
      $form->setData($data);
  
      $form->get('redirect_url')->setValue($data['redirect_url'] ?: '/home');
  
      if( $form->isValid() ) {
        $adapter->setIdentity($data['username']);
        $adapter->setCredential($data['password']);
        $result = $authService->authenticate();
  
        if($result->isValid()) {
          $authService->getStorage()->write($result->getIdentity());
          $controller->redirect()->toUrl($data['redirect_url'] ?: '/home');
        }
  
        if( $result->getCode() == AuthenticationAdapter::OTP_QR_NEEDED ) {
          $childView = new ViewModel();
          /** @var \extCMS\GoogleAuthenticator\GoogleAuthenticator $GA */
          $GA = $this->getServiceLocator()->get('GA');
          $img = $GA->getUrl($this->getServiceLocator()->get('extCMS')->get('sitename'), $result->getMessages()['identity_object']->getSecret());
          $childView->setTemplate('ext-cms/manager/ga-qr-code');
          $childView->setVariable('img', $img);
          $view->addChild($childView);
          return $view;
  
        }
  
        $childView->setVariable('message', 'Credentials Wrong!!');
  
        $view->addChild($childView);
        return $view;
      }
    }
  
    $view->addChild($childView);
    return $view;
  }
  public function canonalizeAlias ( $pagetitle )
  {
    if(empty($pagetitle))
      throw new ManagerException("Can't canonalize empty pagetitle");
  
      return str_replace(' ', '_', $pagetitle);
  }
}