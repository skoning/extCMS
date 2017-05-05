<?php
namespace extCMS\twigExtensions;
 
use Twig_Extension;

class teest extends Twig_Extension 
{
  
  public function getName() 
  {
    return 'teest';
  }
  
  public function __invoke($$test = null)
  {
    var_dump($test);;
  }
}