<?php
namespace extCMS\Controller;

use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use extCMS\Factories\AbstractControllerFactory;
use Zend\Http\Response;

class ListController extends AbstractControllerFactory
{
  protected $em;
  
  protected $sl;
  
  /**
   * @return Doctrine\ORM\EntityManager
   */
  public function getEntityManager()
  {
    if(null === $this->em) {
      $this->em = $this->sl->get('doctrine.entitymanager.orm_default');
    }
    return $this->em;
  }
  
  public function indexAction()
  {
    $page = $this->getEntityManager()->getRepository('extCMS\Entity\Document')->findOneBy(array('alias' => $this->params()->fromRoute('alias')));
    if( !$page ) {
      
      // Page not found
      $response = $this->getResponse();
      $response->setStatusCode(Response::STATUS_CODE_404);
      
      $page = $this->getEntityManager()->getRepository('extCMS\Entity\Document')->findOneBy(array('id' => $this->sl->get('extCMSConfig')->error_page));
    }
    
    return new ViewModel(array('page' => $page));
    
  }
  
}