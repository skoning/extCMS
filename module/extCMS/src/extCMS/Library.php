<?php
namespace extCMS;

use Exception;
/**
 *
 * @author sander
 *        
 */
class Library
{
  public static function canonalizeAlias ( $pagetitle ) 
  {
    if(empty($pagetitle))
      throw new Exception("Can't canonalize empty pagetitle");
    
    return str_replace(' ', '_', $pagetitle);
  }
}