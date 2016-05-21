<?php
namespace extCMS\Manager\Forms;

use Zend\Form\Form;

class ConfigForm extends Form
{

  public function __construct($name = null)
  {

    parent::__construct($name);

    $this->setAttribute('method', 'post');
    $this->add(array(
      'name' => 'key',
      'type' => 'text'
    ));
    $this->add(array(
      'name' => 'value',
      'type' => 'text'
    ));
    
    $this->add(array(
      'name' => 'submit',
      'type' => 'submit',
      'attributes' => array(
        'value' => 'Add',
        'id' => 'submitButton'
      )
    ));
    $this->add(array(
      'name' => 'isNew',
      'type' => 'hidden',
      'attributes' => array(
        'value' => false
      )
    ));
  }
}