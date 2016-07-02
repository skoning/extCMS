<?php
namespace extCMS\Manager;

use DoctrineModule\Authentication\Adapter\ObjectRepository;
use Zend\Authentication\Adapter\Exception;
use Zend\Authentication\Result as AuthenticationResult;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationAdapter extends ObjectRepository
{
  const OTP_QR_NEEDED = -5;
  
  private $otp = false;
  
  private $sl = null;
  
  public function __construct($options, $otp, ServiceLocatorInterface $sl)
  {
    $this->otp = $otp;
    $this->sl = $sl;
    parent::__construct($options);
  }
  
  public function validateIdentity($identity) {
    $credentialProperty = $this->options->getCredentialProperty();
    $getter             = 'get' . ucfirst($credentialProperty);
    $documentCredential = null;
    
    if (method_exists($identity, $getter)) {
      $documentCredential = $identity->$getter();
    } elseif (property_exists($identity, $credentialProperty)) {
      $documentCredential = $identity->{$credentialProperty};
    } else {
      throw new Exception\UnexpectedValueException(
          sprintf(
              'Property (%s) in (%s) is not accessible. You should implement %s::%s()',
              $credentialProperty,
              get_class($identity),
              get_class($identity),
              $getter
              )
          );
    }
    
    $credentialValue = $this->credential;
    $callable        = $this->options->getCredentialCallable();
    
    if ($callable) {
      $credentialValue = call_user_func($callable, $identity, $credentialValue);
    }
    
    if( $this->otp  && is_array($credentialValue) && $credentialValue['action'] == "showQR" ) {
      $this->authenticationResultInfo['code'] = self::OTP_QR_NEEDED;
      $this->authenticationResultInfo['messages']['identity_object'] = $identity;
      return $this->createAuthenticationResult();
    }
    
    if ($credentialValue !== true && $credentialValue !== $documentCredential) {
      $this->authenticationResultInfo['code']       = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
      $this->authenticationResultInfo['messages'][] = 'Supplied credential is invalid.';
    
      return $this->createAuthenticationResult();
    }
    
    $this->authenticationResultInfo['code']       = AuthenticationResult::SUCCESS;
    $this->authenticationResultInfo['identity']   = $identity;
    $this->authenticationResultInfo['messages'][] = 'Authentication successful.';
    
    return $this->createAuthenticationResult();
  }
}