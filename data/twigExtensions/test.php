<?php
namespace extCMS\twigExtensions;
 
use Twig_Extension;

class test extends Twig_Extension 
{
  
  public function getName() 
  {
    return 'test';
  }
  
  public function __invoke($One, $two, $three)
  {
    echo $One;
  }
}
