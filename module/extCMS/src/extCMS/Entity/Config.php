<?php
namespace extCMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;

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
  
  /**
   * @ORM\Column( name="type", type="string", length=20 )
   * @var string
   */
  protected $type;
  
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
  
  public function getValue( $useType = true )
  {
    if( !$useType ) 
      return $this->value;
    
    switch( $this->type ) {
      case 'boolean':
        return ( $this->value == 'false' || $this->value == '0') ? false : true;
      case 'string':
        return $this->value;
      default:
        throw new Exception('Type not implemented');
    }
  }
  
  public function exchangeArray($data)
  {
    $this->key = isset($data['key']) ? $data['key'] : null;
    $this->value = isset($data['value']) ? $data['value'] : null;
  }
  
  public function getArray()
  {
    $arr = array();
    $arr['key'] = $this->getKey();
    $arr['value'] = $this->getValue(false);
    return $arr;
  }
}