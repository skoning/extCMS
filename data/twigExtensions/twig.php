<?php
namespace extCMS	wigExtensions;
 
use Twig_Extension;

class twig extends Twig_Extension 
{
  
  public function getName() 
  {
    return twig;
  }
  
  public function __invoke($$bloa)
  {
    echo $bloa;;
  }
}