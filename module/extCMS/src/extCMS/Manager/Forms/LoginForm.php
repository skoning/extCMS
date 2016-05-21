<?php
namespace extCMS\Manager\Forms;

use Zend\Form\Form;

class LoginForm extends Form
{
  public function __construct($name = null)
  {
    parent::__construct($name);
    
    $this->setAttribute('method', 'post');
    $this->add(array(
      'name' => 'username',
      'type' => 'text',
      'options' => array(
        'label' => 'Username',
        'label_attributes' => array(
//           'class' => 'col-sm-3'
        ),
      )
    ));
    $this->add(array(
      'name' => 'password',
      'type' => 'password',
      'options' => array(
        'label' => 'Password',
        'label_attributes' => array(
//           'class' => 'col-sm-3'
        ),
      )
    ));
    $this->add(array(
      'name' => 'login_csrf',
      'type' => 'csrf',
      'options' => array(
        'csrf_options' => array(
          'timeout' => 600
        )
      )
    ));
    $this->add(array(
      'name' => 'redirect_url',
      'type' => 'hidden'
    ));
    $this->add(array(
      'name' => 'login',
      'type' => 'submit',
      'attributes' => array(
        'value' => 'Login'
      )
    ));
    
  }
}