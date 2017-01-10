<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Comment;


class Comments
{
    private $doctrine;

    public function __construct($doctrine){
      $this->doctrine=$doctrine;
    }

    public function getCommentsCount($articleID)
        {
            return $this->doctrine->getRepository("AppBundle:Comment")
                    ->countCommentsByArticleID($articleID);
        }
}
