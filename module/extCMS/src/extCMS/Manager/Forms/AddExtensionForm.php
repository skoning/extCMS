<?php
namespace extCMS\Manager\Forms;

use Zend\Form\Form;

class AddExtensionForm extends Form
{
  
  public function __construct($name = null)
  {
    
    parent::__construct('extensionAdd');
    
    $this->setAttribute('method', 'post');
    $this->add(array(
        'name' => 'name',
        'type' => 'Text',
        'options' => array(
          'label' => 'Extension Name'
        )
    ));
    
    $this->add(array(
        'name' => 'arguments',
        'type' => 'Text',
        'options' => array(
            'label' => 'Arguments',
            'maxlength' => 255
        )
    ));
    
    $this->add(array(
      'name' => 'code',
      'type' => 'textarea',
      'options' => array(
        'label' => 'Code'
      ),
      'attributes' => array(
        'cols' => 200,
        'rows' => 40
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