<?php
namespace extCMS\Manager;

use extCMS\Entity\Config;
use extCMS\Factories\ManagerControllerFactory;
use extCMS\Manager\Forms\ConfigForm;
use extCMS\Manager\Forms\LoginForm;
use Zend\Authentication\AuthenticationService;
use extCMS\Exceptions\ManagerException;
use Zend\View\Model\ViewModel;

class ManagerController extends ManagerControllerFactory
{
  
  /**
   * The loginAction
   * Check credentials and when correct redirect to requested URL
   */
  public function loginAction()
  {
    return $this->getServiceLocator()->get('extCMSManager')->login( $this->getRequest(), $this );
    /*$view = new ViewModel();
    $childView = new ViewModel();
    $childView->setTemplate('ext-cms/manager/login');
    $view->setTemplate('managerLayout');
    
    $form = new LoginForm('login');
    $form->setAttribute('action', $this->url('manager/login'));
    $request = $this->getRequest();
    
    $referer = $request->getQuery('redirect_url');
    $form->get('redirect_url')->setValue($referer);
    
    /**
     * @var AuthenticationService $authService
     * /
    $authService = $this->getServiceLocator()->get('extCMSAuthenticationService');
    $adapter = $authService->getAdapter();
    
    $childView->setVariable('form', $form);
    
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
          $this->redirect()->toUrl($data['redirect_url'] ?: '/home');
        }
        
        if( $result->getCode() == AuthenticationAdapter::OTP_QR_NEEDED ) {
          $childView = new ViewModel();
          /** @var \extCMS\GoogleAuthenticator\GoogleAuthenticator $GA * /
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
    */
  }
  
  /**
   * Action to manage configuration
   * @throws ManagerException
   */
  public function setAction()
  {
    if( !$this->isLoggedIn )
      $this->loginRedirect();
    
    $translator = $this->getServiceLocator()->get('translator');
    $forms = array();
    $request = $this->getRequest();
    if ($request->isPost() && $request->getPost()['isNew']) {
      if( $this->getEntityManager()->getRepository('extCMS\Entity\Config')->findBy(array('key' => $this->request->getPost()['key'])) !== array()) {
        throw new ManagerException("Key already exits");
      } else {
        $option = new Config();
        $option->exchangeArray($request->getPost());
        $em = $this->getEntityManager();
        $em->persist($option);
        $em->flush();
      }
    }
    $configOptions = $this->getEntityManager()->getRepository('extCMS\Entity\Config')->findAll();
    
    foreach( $configOptions as $option) {
      /** @var Config $option */
      $form = new ConfigForm($option->getKey());
      
      if( $request->isPost() ) {
        
        if( $request->getPost()['key'] == $option->getKey() ) {
          
          $form->setData($request->getPost());
          
          if( $form->isValid() ) {
            
            $option->exchangeArray($form->getData());
            $em = $this->getEntityManager();
            $em->persist($option);
            $em->flush();
            
          }
        } else {
          $form->setData($option->getArray());
        }
      } else {
        
        $form->setData($option->getArray());
      
      }        
      
      $form->get('submit')->setValue($translator->translate('Save'));
      $forms[] = $form;

    }
    
    $newForm = new ConfigForm();
    $newForm->get('isNew')->setValue(true);
    $newForm->get('submit')->setValue($translator->translate('Add'));
    
    $forms[] = $newForm;
    
    return array('forms' => $forms);
  }
}