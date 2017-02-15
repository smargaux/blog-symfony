<?php
namespace AppBundle\Twig;

class TagExtension extends \Twig_Extension
{

  protected $doctrine;

  public function __construct($doctrine){
    $this->doctrine = $doctrine;
  }
  public function getFunctions()
  {
      return array(
          new \Twig_SimpleFunction('getCountArticlesByTag', array($this, 'getCountArticlesByTag'))
      );
  }
  public function getCountArticlesByTag($tag){
    return $this->doctrine
                  ->getRepository('AppBundle:Article')
                  ->countArticlesByTag($tag);
  }






}

 ?>
