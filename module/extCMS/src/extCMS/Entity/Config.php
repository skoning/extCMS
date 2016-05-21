<?php
namespace extCMS\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table ( name="config" )
 * @author sander
 *
 */
class Config
{
  
  /**
   * @ORM\Id
   * @ORM\Column ( name="`key`", type="string" )
   * @var string
   */
  protected $key;
  
  /**
   * @ORM\Column ( type="string", length=255 )
   * @var string
   */
  protected $value;
  
  public function setKey( $key )
  {
    $this->key = $key;
  }
  
  public function getKey()
  {
    return $this->key;
  }
  
  public function setValue( $value )
  {
    $this->value = $value;
  }
  
  public function getValue()
  {
    return $this->value;
  }
  
  public function exchangeArray($data)
  {
    $this->key = isset($data['key']) ? $data['key'] : null;
    $this->value = isset($data['value']) ? $data['value'] : null;
  }
  
  public function getArray()
  {
    $arr = array();
    $arr['key'] = $this->key;
    $arr['value'] = $this->value;
    return $arr;
  }
}