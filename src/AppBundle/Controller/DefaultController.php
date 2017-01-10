<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // Récupération des cinqs derniers articles publiés
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->getLastFiveArticles();

        return $this->render('index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/articles/{page}", name="article_list",requirements={"page": "\d+"})
     */
    public function articlesListAction(Request $request, $page=1)
    {
      $articlesCount = $this->getDoctrine()->getRepository('AppBundle:Article')->getCountArticlesOnline();
      $countPages=ceil($articlesCount/2);

       $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->getArticlesList($page);
      return $this->render('article/index.html.twig', array(
          'articles' => $articles,'dernierePage'=>$countPages,'currentPage'=>$page
      ));

    }

    /**
     * Finds and displays a article entity.
     *
     * @Route("/article/{id}", name="article_show")
     */
    public function showArticleAction(Request $request,Article $article)
    {

      //$comments=$this->getDoctrine()->getRepository('AppBundle:Comment')->findByArticle($article);
      $comments=$article->getComments();
      $comment = new Comment($article);
      $commentForm = $this->createForm(CommentType::class, $comment);
      $commentForm->handleRequest($request);

      if ($commentForm->isSubmitted() && $commentForm->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($comment);
          $em->flush($comment);

      }


        return $this->render('article/show.html.twig', array(
            'article' => $article,'commentForm'=>$commentForm->createView(),'comments'=>$comments
        ));
    }
}
