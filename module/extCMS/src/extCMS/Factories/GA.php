<?php
namespace extCMS\Factories;

use Zend\ServiceManager\FactoryInterface;
use extCMS\GoogleAuthenticator\GoogleAuthenticator;
use Zend\ServiceManager\ServiceLocatorInterface;

class GA implements FactoryInterface 
{
  public function createService(ServiceLocatorInterface $sm) {
    return new GoogleAuthenticator();
  }
}