<?php
namespace extCMS\Manager\Forms;

use Zend\Form\Form;

class AddForm extends Form
{
  
  public function __construct($name = null)
  {
    
    parent::__construct($name);
    
    $this->setAttribute('method', 'post');
    $this->add(array(
        'name' => 'id',
        'type' => 'hidden'
    ));
    $this->add(array(
        'name' => 'pagetitle',
        'type' => 'Text',
        'options' => array(
          'label' => 'pagetitle'
        )
    ));
    
    $this->add(array(
      'name' => 'alias',
      'type' =>'Text',
      'options' => array(
        'label' => 'URL Alias'
      )
    ));
    $this->add(array(
      'name' => 'content',
      'type' => 'textarea',
      'options' => array(
        'label' => 'content'
      ),
      'attributes' => array(
        'cols' => 200,
        'rows' => 20
      )
    ));
    
    $this->add(array(
      'name' => 'submit',
      'type' => 'Submit',
      'attributes' => array(
        'value' => 'Add',
        'id' => 'submitButton'
      )
    ));
  }
  
}