<?php

namespace AppBundle\Repository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{

  public function getLastFiveArticles(){
  return $this
  ->createQueryBuilder('a')
  ->leftJoin('a.author','au')
  ->addSelect('au')
  ->leftJoin('a.category','c')
  ->addSelect('c')
  ->leftJoin('a.tags','t')
  ->addSelect('t')
  ->orderBy('a.publicationDate','ASC')
  ->setMaxResults(5)
  ->getQuery()
  ->getArrayResult();
  }}
