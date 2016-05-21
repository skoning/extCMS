<?php
namespace extCMS\twigExtensions;
 
use Zend\View\Helper\AbstractHelper;

class Functions extends AbstractHelper {
  
  public function __invoke($test1)
  {
    var_dump(func_get_args());;
  }
}