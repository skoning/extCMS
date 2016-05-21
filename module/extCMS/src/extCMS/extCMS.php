<?php
namespace extCMS;

use Doctrine\ORM\EntityManager;

class extCMS
{
  protected $em;
  
  public function setEntityManager( EntityManager $em )
  {
    $this->em = $em;
  }
}