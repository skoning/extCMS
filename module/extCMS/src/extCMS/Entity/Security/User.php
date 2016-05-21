<?php
namespace extCMS\Entity\Security;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="manager_users")
 *
 */
class User 
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
   * @ORM\Column(name="password", type="string", length=64)
   * @var string
   */
  protected $password;
  
  public function setUsername( $username )
  {
    $this->username = $username;
    return $this;
  }
  
  public function getUsername()
  {
    return $this->username;
  }
  public function getPassword()
  {
    return $this->password;
  }
  
  public function validatePassword( $passwordGiven )
  {
    return hash('sha256', $passwordGiven);
  }
}