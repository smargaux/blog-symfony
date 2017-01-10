<?php
namespace AppBundle\Twig;

class ArticleExtension extends \Twig_Extension
{

  private $commentService;

  public function __construct($service){
    $this->commentService=$service;
  }
  public function getFunctions()
  {
      return array(
          new \Twig_SimpleFunction('getCommentsCount', array($this, 'getCommentsCount'))
      );
  }
  public function getCommentsCount($articleID){
    return $this->commentService->getCommentsCount($articleID);

  }






}

 ?>
