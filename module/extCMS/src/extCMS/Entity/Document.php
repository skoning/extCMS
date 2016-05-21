<?php
namespace extCMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
/**
 * 
 * @ORM\Entity
 * @ORM\Table(name="resource")
 * 
 */
class Document implements InputFilterAwareInterface
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
   * @ORM\Column(type="string", unique=true, length=255)
   * @var string
   * 
   */
  protected $pagetitle;
  
  /**
   * @ORM\Column(type="string", unique=true, length=255)
   * @var string
   */
  protected $alias;
  
  /**
   * @ORM\Column(type="text")
   * @var string
   */
  protected $content;
  
  protected $inputFilter;
  
  
  
  public function getId()
  {
    return $this->id;
  }
  
 
  public function getPagetitle()
  {
    return $this->pagetitle;
  }
  
  public function getContent()
  {
    return $this->content;
  }
  
  public function setContent($content)
  {
    $this->content = $content;
  }
  
  public function setPagetitle($title)
  {
    $this->pagetitle = $title;
  }
  
  public function exchangeArray($data)
  {
    $this->id = (isset($data['id'])) ? $data['id'] : null;
    $this->pagetitle = (isset($data['pagetitle'])) ? trim($data['pagetitle']) : null;
    $this->alias = ( isset($data['alias']) ? $data['alias'] : str_replace(' ', '_', $this->pagetitle));
    $this->content = (isset($data['content'])) ? trim($data['content']) : null;
  }
  
  public function getArray()
  {
    $arr = array();
    $arr['id'] = $this->id;
    $arr['pagetitle'] = $this->pagetitle;
    $arr['content'] = $this->content;
    
    return $arr;
  }
  
  public function setInputFilter(InputFilterInterface $inputFilter)
  {
    throw new \Exception("Not Used");
  }
  
  public function getInputFilter()
  {
    if (!$this->inputFilter) {
      $inputFilter = new InputFilter();
      $inputFilter->add(array(
        'name' => 'pagetitle',
        'required' => true,
        'filters' => array(
          array('name' => 'StripTags'),
          array('name' => 'StringTrim'),
        ),
        'vallidators' => array(
          array(
            'name' => 'StringLength',
            'options' => array(
              'encoding' => 'UTF-8',
              'min' => 1,
              'max' => 254
            )
          )
        )
      ));
      $inputFilter->add(array(
          'name' => 'alias',
          'required' => true,
          'filters' => array(
              array('name' => 'StripTags'),
              array('name' => 'StringTrim'),
          ),
          'vallidators' => array(
              array(
                  'name' => 'StringLength',
                  'options' => array(
                      'encoding' => 'UTF-8',
                      'min' => 1,
                      'max' => 254
                  )
              )
          )
      ));
      $this->inputFilter =  $inputFilter;
    }
    
    return $this->inputFilter;
  }
}