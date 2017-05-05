<?php
namespace extCMS\Services;

use Doctrine\ORM\EntityManager;
use extCMS\Exceptions\ManagerException;

class extCMS
{
  protected $em;
  
  public function setEntityManager( EntityManager $em )
  {
    $this->em = $em;
  }
  
  public function canonalizeAlias ( $pagetitle )
  {
    if(empty($pagetitle))
      throw new ManagerException("Can't canonalize empty pagetitle");
  
      return str_replace(' ', '_', $pagetitle);
  }
}