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
              ->setFirstResult(($page-1)*10)
              ->setMaxResults(10)
              ->getQuery()
              ->execute();
      return $query;
  }

  /**
   * Récupère les derniers articles publiés (date de publication <=aujourd'hui), paginé avec 10 articles par page
   * selon les critères nom et tags choisis
   * @param  integer $page    numéro de la page en cours
   */
    public function getArticlesBySearchList($page,$name,$tags)
    {
        $query =$this->createQueryBuilder('a')
                ->leftJoin('a.tags','t')
                ->where('a.publicationDate <= :today')
                ->andWhere('a.name LIKE :name')
                ->andWhere('t.id IN (:tags)')
                ->setParameter('today', new \DateTime())
                ->setParameter('name', '%'.$name.'%')
                ->setParameter('tags', $tags)
                ->setFirstResult(($page-1)*10)
                ->setMaxResults(10)
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

  /**
   * Récupère les derniers articles publiés  des résultats de la recherche (date de publication <=aujourd'hui), paginé avec 10 articles par page
   * @param  integer $page    numéro de la page en cours
   */
    public function getArticlesByCategory($name,$page=1)
    {
        $query =$this->createQueryBuilder('a')
                    ->leftJoin('a.category','c')
                    ->orderBy('a.publicationDate', 'DESC')
                    ->where('a.publicationDate <= :today')
                    ->andWhere('c.name LIKE :category')
                    ->setParameter('today', new \DateTime())
                    ->setParameter('category', $name)
                    ->setFirstResult(($page-1)*10)
                    ->setMaxResults(10)
                    ->getQuery()
                    ->execute();
        return $query;
    }
    /**
     * Récupère les derniers articles publiés  des résultats de la recherche (date de publication <=aujourd'hui), paginé avec 10 articles par page
     * @param  integer $page    numéro de la page en cours
     */
      public function getArticlesBySearchCategory($name,$tags,$nameArticle)
      {
          $query =$this->createQueryBuilder('a')
                      ->leftJoin('a.category','c')
                      ->leftJoin('a.tags','t')
                      ->orderBy('a.publicationDate', 'DESC')
                      ->where('a.publicationDate <= :today')
                      ->andWhere('c.name LIKE :category')
                      ->andWhere('a.name LIKE :name')
                      ->andWhere('t.id IN (:tags)')
                      ->setParameter('today', new \DateTime())
                      ->setParameter('category', $name)
                      ->setParameter('name', '%'.$nameArticle.'%')
                      ->setParameter('tags', $tags)
                      ->getQuery()
                      ->execute();
          return $query;
      }

    /**
     * Compte les articles publiés pour gérer la pagination
     */
    public function getCountArticlesOnlineByCategory($name)
    {
        return $this ->createQueryBuilder('a')
                     ->select('count(a.id)')
                     ->leftJoin('a.category','c')
                     ->where('a.publicationDate <= :today')
                     ->andWhere('c.name LIKE :category')
                     ->setParameter('category', $name)
                     ->setParameter('today', new \DateTime())
                     ->getQuery()
                     ->getSingleScalarResult();
    }

    /**
    * Compte le nombre d'articles par tag
    **/
    public function countArticlesByTag($tag){
      return $this ->createQueryBuilder('a')
                   ->select('count(a.id)')
                   ->leftJoin('a.tags','t')
                   ->where('t.id= :tag')
                   ->setParameter('tag', $tag)
                   ->getQuery()
                   ->getSingleScalarResult();
    }

    /**
    * Compte le nombre d'articles par catégorie
    **/
    public function countArticlesByCategory($category){
      return $this ->createQueryBuilder('a')
                   ->select('count(a.id)')
                   ->leftJoin('a.category','c')
                   ->where('c.id= :category')
                   ->setParameter('category', $category)
                   ->getQuery()
                   ->getSingleScalarResult();
    }
}
