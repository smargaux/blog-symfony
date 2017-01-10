<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
    /**
 * Récupère les cinq derniers articles publiés (date de publication <=aujourd'hui) pour les afficher sur la page d'accueil
 */
  public function getLastFiveArticles()
  {
      return $this
            ->createQueryBuilder('a')
            ->orderBy('a.publicationDate', 'DESC')
            ->where('a.publicationDate <= :today')
            ->setParameter('today', new \DateTime())
            ->setMaxResults(5)
            ->getQuery()
            ->execute();
  }

/**
 * Récupère les derniers articles publiés (date de publication <=aujourd'hui), paginé avec 10 articles par page
 * @param  integer $page    numéro de la page en cours
 */
  public function getArticlesList($page=1)
  {
      $query =$this->createQueryBuilder('a')
      ->orderBy('a.publicationDate', 'DESC')

              ->where('a.publicationDate <= :today')
              ->setParameter('today', new \DateTime())
              ->setFirstResult(($page-1)*2)
              ->setMaxResults(2)
              ->getQuery()
              ->execute();
      return $query;
  }

  /**
   * Compte les articles publiés pour gérer la pagination
   */
  public function getCountArticlesOnline()
  {
      return $this ->createQueryBuilder('a')
                   ->select('count(a.id)')
                   ->where('a.publicationDate <= :today')
                   ->setParameter('today', new \DateTime())
                   ->getQuery()
                   ->getSingleScalarResult();
  }
}
