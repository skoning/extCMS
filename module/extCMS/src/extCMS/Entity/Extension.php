<?php
namespace extCMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
/**
 * @ORM\Entity
 * @ORM\Table(name="extension")
 */
class Extension
{
 
  /**
   *
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @var integer
   */
  protected $id;
  
  /**
   * @ORM\Column(type="string",length=40,unique=true)
   * @var string
   */
  protected $name;
  
  /**
   * @ORM\Column(type="text")
   */
  protected $code;
  
  protected $inputFilter;
  
  public function getId() 
  {
    return $this->id;
  }
  
  public function getName() 
  {
    return $this->name;
  }
  
  public function getCode()
  {
    return $this->code;
  }
  
  public function setName($name)
  {
    $this->name = $name;
  }
  
  public function setCode($code)
  {
    $this->code = $code;
  }
    
  public function setInputFilter(InputFilterInterface $inputFilter)
  {
    throw new \Exception("Not Used");
  }
  
  public function getInputFilter()
  {
    if( !$this->inputFilter) {
      $inputFilter = new InputFilter();
      $inputFilter->add(array(
        'name' => 'name',
        'required' => true,
        'filters' => array(
          array('name' => 'StripTags'),
          array('name' => 'StringTrim'),
        ),
        'vallidators' => array(
          'name' => 'StringLength',
          'options' => array(
            'encoding' => 'UTF-8',
            'min' => 1,
            'max' => 40
          )
        )
      ));
      $this->inputFilter = $inputFilter;
    }
    
    return $this->inputFilter;
  }
  
  public function exchangeArray($data)
  {
    $this->id = ( isset($data['id']) ? $data['id'] : null);
    $this->name = ( isset($data['name']) ? $data['name'] : null);
    $this->code = ( isset($data['code']) ? $data['code'] : null);
  }
}