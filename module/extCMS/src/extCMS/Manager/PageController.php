<?php
namespace extCMS\Manager;

use extCMS\Entity\Document;
use extCMS\Entity\Extension;
use extCMS\Exceptions\ManagerException;
use extCMS\Exceptions\PageNotFoundException;
use extCMS\Factories\ManagerControllerFactory;
use extCMS\Library;
use extCMS\Manager\Forms\AddExtensionForm;
use extCMS\Manager\Forms\AddForm;


class PageController extends ManagerControllerFactory
{

  /**
   * addAction
   * To add a new page
   */
  public function addAction()
  {
    $this->loginRedirect();
    
    $form = new AddForm();
    $form->setAttribute('action', $this->url('manager', array('action' => 'add') ));

    $request = $this->getRequest();
    if( $request->isPost()) {
      $document = new Document();
      $form->setInputFilter($document->getInputFilter());
      
      $data = $request->getPost();
      $data['alias'] = (!empty($data['alias'])) ? $data['alias'] : $this->getServiceLocator()->get('extCMS')->canonalizeAlias($data['pagetitle']);
      
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

  /**
   * extensionAction 
   * To create a new extension
   * @throws ManagerException
   */
  public function extensionAction()
  {
    $this->loginRedirect();
    
    $form = new AddExtensionForm();
    $form->setAttribute('action', $this->url('manager', array('action' => 'extension')));

    $request = $this->getRequest();
    if( $request->isPost()) {
      $em = $this->getEntityManager();
      $extension = new Extension();

      $form->setInputFilter($extension->getInputFilter());
      $form->setData($request->getPost());

      if( $form->isValid() ) {
        $extension = $em->getRepository('extCMS\Entity\Extension')->findOneBy(array('name' => $request->getPost()['name']));
        if( $extension ) {} else {
          $extension = new Extension();
        }
        $extension->exchangeArray($request->getPost());
          

        $arguments = explode(';', $request->getPost()['arguments']);
        foreach ($arguments as $key => $value) {
          $arguments[$key] = '$' . $value;
        }
        $arguments = implode(', ', $arguments);
        $code = <<<EOT
<?php
namespace extCMS\\twigExtensions;
 
use Twig_Extension;

class {$extension->getName()} extends Twig_Extension 
{
  
  public function getName() 
  {
    return '{$extension->getName()}';
  }
  
  public function __invoke({$arguments})
  {
    {$extension->getCode()};
  }
}
EOT;
        $filename = 'data/twigExtensions/' . DIRECTORY_SEPARATOR . $extension->getName() . '.php';
        echo $filename;
        if( !is_writable($filename) ) 
          throw new ManagerException('File not writable!!');
        $fh = fopen($filename, 'wb');
        clearstatcache(true);
        chmod($filename, '0764');
        
        if($fh) {
          if( fwrite($fh, $code, strlen($code)) !== false ) {
            $em->persist($extension);
            $em->flush();
          } else 
            throw new ManagerException("Could not save your extension. Could not write to file.");
          fclose($fh);
        } else {
          throw new ManagerException("Could not save your extension. Could not open file.");
        }
      }
    } 
    return array('form' => $form);
  }

  /**
   * editAction 
   * To edit a page
   * 
   * @throws ManagerException
   * @throws PageNotFoundException
   * @return string[]|\extCMS\Manager\Forms\AddForm[]|\extCMS\Entity\Document[]|object[]|NULL[]
   */
  public function editAction()
  {
    $this->loginRedirect();
    
    $id = $this->params()->fromRoute('id');
    if( $id == null ) {
      throw new ManagerException('No identifier given!');
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
  }
}