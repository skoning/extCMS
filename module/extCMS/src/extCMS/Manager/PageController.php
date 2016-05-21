<?php
namespace extCMS\Manager;

use RuntimeException;
use extCMS\Entity\Document;
use extCMS\Manager\Forms\AddForm;
use extCMS\Manager\Forms\AddExtensionForm;
use extCMS\Entity\Extension;
use extCMS\Factories\ManagerControllerFactory;
use extCMS\Exceptions\PageNotFoundException;
use extCMS\Library;

use Zend\View\Model\ViewModel;
class PageController extends ManagerControllerFactory
{

  public function addAction()
  {
    if(!$this->isLoggedIn)
      $this->redirect()->toUrl($this->url()->fromRoute('manager/login') . '?redirect_url=/manager/add');
    
    $form = new AddForm();
    $form->setAttribute('action', $this->url('manager', array('action' => 'add') ));

    $request = $this->getRequest();
    if( $request->isPost()) {
      $document = new Document();
      $form->setInputFilter($document->getInputFilter());
      
      $data = $request->getPost();
      $data['alias'] = Library::canonalizeAlias($data['pagetitle']);
      
      $form->setData($request->getPost());
      
      $em = $this->getEntityManager();

      if( $form->isValid() ) {
        $document->exchangeArray($form->getData());
        $em = $this->em;
        $em->persist($document);
        $em->flush();
        echo "Stored";
      }
      
    }

    return array('form' => $form, 'action' => 'add');
  }

  public function extensionAction()
  {
    $form = new AddExtensionForm();
    $form->setAttribute('action', $this->url('manager', array('action' => 'extension')));

    $request = $this->getRequest();
    if( $request->isPost()) {
      $extension = new Extension();
      $form->setInputFilter($extension->getInputFilter());
      $form->setData($request->getPost());

      if( $form->isValid() ) {
        $extension->exchangeArray($request->getPost());
        $em = $this->getEntityManager();

        $arguments = explode(';', $request->getPost()['arguments']);
        foreach ($arguments as $key => $value) {
          $arguments[$key] = '$' . $value;
        }
        $arguments = implode(', ', $arguments);
        $code = <<<EOT
<?php
namespace extCMS\\twigExtensions;

use Zend\\View\\Helper\\AbstractHelper;

class {$extension->getName()} extends AbstractHelper {

  public function __invoke({$arguments})
  {
    {$extension->getCode()};
  }
}
EOT;
    $fh = fopen(realpath('data/twigExtensions/') . DIRECTORY_SEPARATOR . $extension->getName() . '.php', 'wb');
    if($fh) {
      fwrite($fh, $code, strlen($code));
      fclose($fh);
      $em->persist($extension);
      $em->flush();
    } else {
      var_dump($fh);
      throw new RuntimeException("Could not save your extension");
    }
      }
    }
     
    return array('form' => $form);
  }

  public function editAction()
  {
    try {

      $id = $this->params()->fromRoute('id');
      if( $id == null ) {
        throw new RuntimeException('No identifier given!');
      }

      $page = $this->getEntityManager()->find('extCMS\Entity\Document', $id);

      if( !( $page instanceof Document ) ) {
        throw new PageNotFoundException('Page not found');
      }

      $form = new AddForm('EditForm');

      $request = $this->getRequest();
      if( !$request->isPost() ) {
        $form->setData($page->getArray());

      } else if ( $request->isPost() ) {
        $form->setData($request->getPost());
        if( $form->isValid() ) {
          $em = $this->getEntityManager();
          $page->exchangeArray( $form->getData() );
          $em->persist($page);
          $em->flush();
        }
      }

      return array('action' => 'edit', 'form' => $form, 'page' => $page);

    } catch ( PageNotFoundException $e ) {
      $page = new Document();
      $page->setContent( $e->getMessage() );
      $view =  new ViewModel();
      $view->setTemplate('ext-cms/errors/managerError')->setVariable('page', $page);
      return $view;
    }
    return array('page' => $page);
  }
}