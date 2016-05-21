<?php
namespace extCMS\twigExtensions;

use Zend\View\Helper\AbstractHelper;

class MyFunction extends AbstractHelper {
  
  protected $sm;
  protected $viewManager;
  
  public function __construct($sm) {
    $this->sm = $sm;
  }
  
  public function __invoke($test = 'test321')
  {
    return $test;
  }
  
}