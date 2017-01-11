<?php
namespace AppBundle\Twig;

class CategoryExtension extends \Twig_Extension
{

  protected $doctrine;

  public function __construct($doctrine){
    $this->doctrine = $doctrine;
  }
  public function getFunctions()
  {
      return array(
          new \Twig_SimpleFunction('getCategories', array($this, 'getCategories'))
      );
  }
  public function getCategories(){
  return $this->doctrine
                ->getRepository('AppBundle:Category')
                ->findAll();
  }






}

 ?>
