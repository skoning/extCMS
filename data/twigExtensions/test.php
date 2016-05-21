<?php
namespace extCMS\twigExtensions;
 
use Zend\View\Helper\AbstractHelper;

class test extends AbstractHelper {
  
  public function __invoke($One, $two, $three)
  {
    echo $One;
  }
}
