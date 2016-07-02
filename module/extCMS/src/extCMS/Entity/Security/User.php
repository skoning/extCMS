<?php
namespace extCMS\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use extCMS\Listener\ServiceLocatorAwareEntity;
use extCMS\extCMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="manager_users")
 * @ORM\HasLifecycleCallbacks
 *
 */
class User extends ServiceLocatorAwareEntity
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @var integer
   */
  protected $id;
  
  /**
   * @ORM\Column(name="username", type="string", length=50, unique=true)
   * @var string
   */
  protected $username;
  
  /**
   * The hashed password.
   * We sha256 to hash the password
   * @ORM\Column(name="password", type="string", length=64)
   * @var string
   */
  protected $password;
  
  /**
   * The secret for GA
   * @ORM\Column(name="secret", type="string", length=16)
   * @var string
   */
  protected $secret;
  
  /**
   * Set the username (identity)
   * @param string $username
   * @return \extCMS\Entity\Security\User
   */
  public function setUsername( $username )
  {
    $this->username = $username;
    return $this;
  }
  
  /**
   * Set the password after hashing it
   * @param string $password
   */
  public function setPassword( $password )
  {
    $this->password = hash('sha256', $password);
    return $this;
  }
  
  /**
   * Get the users identity (username)
   */
  public function getUsername()
  {
    return $this->username;
  }
  
  /**
   * Get the users hashed password
   */
  public function getPassword()
  {
    return $this->password;
  }
  
  /**
   * Get the GA secret for onetime-password
   */
  public function getSecret()
  {
    if( empty( $this->secret) ) {
      $this->secret = $this->getServiceLocator()->get('GA')->generateSecret();
      $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
      $em->persist($this);
      $em->flush();
    }
    return $this->secret;
  }
  
  /**
   * Check the given password against the hash in the database
   * @param string $passwordGiven
   * @return boolean
   */
  public function validatePassword( $passwordGiven, $useOTP = true )
  {
    $extCMSConfig = $this->getServiceLocator()->get('extCMSConfig');
    if(!is_object($extCMSConfig)) {
      throw new \Exception('Not an object!!');
    }
    if( $this->getServiceLocator()->get('extCMSConfig')->get('otp-enabled') ) {
      if( $useOTP ) {
        $otp = substr($passwordGiven, -6);
        $passwordGiven2 = substr($passwordGiven, 0, -6);
        
        /** @var \extCMS\GoogleAuthenticator\GoogleAuthenticator $ga */
        $ga = $this->getServiceLocator()->get('GA');
        
        if( $ga->checkCode($this->getSecret(), $otp) && hash('sha256', $passwordGiven2) == $this->getPassword() ) {
          return true;
        } else { 
          if(hash('sha256', $passwordGiven) == $this->getPassword()) {
            return array('valid' => hash('sha256', $passwordGiven) == $this->getPassword(), 'action' => 'showQR');
          }
        }
      }
    } else {
      return hash('sha256', $passwordGiven) == $this->getPassword();
    }
  }
}