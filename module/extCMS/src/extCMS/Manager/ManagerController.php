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
    $view = $this->getEvent()->getViewModel();
    $view->setTemplate('managerLayout');
    
    $childView = new ViewModel();
    $childView->setTemplate('ext-cms/manager/login');
    
    $form = new LoginForm('login');
    $form->setAttribute('action', $this->url('manager/login'));
    $request = $this->getRequest();
    
    $referer = $request->getQuery('redirect_url');
    $form->get('redirect_url')->setValue($referer);
    
    /**
     * @var AuthenticationService $authService
     */
    $authService = $this->getServiceLocator()->get('extCMSAuthenticationService');
    $adapter = $authService->getAdapter();
    
    if ($request->isPost()) {
      $data = $request->getPost();
      $form->setData($data);
      
      $form->get('redirect_url')->setValue($data['redirect_url'] ?: '/home');
      
      if( $form->isValid() ) {
        $adapter->setIdentity($data['username']);
        $adapter->setCredential($data['password']);
        $result = $authService->authenticate();
        
        if ($result->isValid()) {
          $authService->getStorage()->write($result->getIdentity());
          $this->redirect()->toUrl($data['redirect_url'] ?: '/home');
        }
        return array('form' => $form, 'message' => 'Credentials wrong');
      }
    }
    
    $view->addChild($childView);
    $childView->form = $form;
    return $view;
  }
  
  public function setAction()
  {
    if( !$this->isLoggedIn )
      $this->loginRedirect();
    
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
    
      $forms[] = $form;

    }
    $newForm = new ConfigForm();
    $newForm->get('isNew')->setValue(true);
    
    $forms[] = $newForm;
    
    return array('forms' => $forms, 'msg' => $msg);
  }
}