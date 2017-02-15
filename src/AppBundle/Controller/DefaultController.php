<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use AppBundle\Form\SearchArticleType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // Récupération des cinqs derniers articles publiés
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->getLastFiveArticles();
        return $this->render('index.html.twig', array(
            'articles' => $articles
        ));
    }

    /**
     * @Route("/articles/{page}", name="article_list",requirements={"page": "\d+"})
     */
    public function articlesListAction(Request $request, $page=1)
    {


      // Formulaire de recherche par nom et/ou tags d'article
      $formSearch=$this->createForm(SearchArticleType::class);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // On récupère les données du formulaire
        $data=$request->request->get("search_article");

            if (array_key_exists('name', $data)) {
                $name = $data['name'];
            } else {
                $name ="";
            }
        // Si aucun tag n'est sélectionné on les récupère tous pour ne pas fausser la recherche

        if (array_key_exists('tags', $data)) {
            $tags = $data['tags'];
        } else {
            $tags=$this->getDoctrine()->getRepository('AppBundle:Tag')->findAll();
        }

            $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->getArticlesBySearchList($page, $name, $tags);
        // On retourne le résultat sans la pagination
        return $this->render('article/index.html.twig', array(
            'articles' => $articles,'searchForm'=>$formSearch->createView()
        ));
        } else {
            // On compte le nombre d'articles en ligne (date de publication <= aujourd'hui)
        $articlesCount = $this->getDoctrine()->getRepository('AppBundle:Article')->getCountArticlesOnline();
            $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->getArticlesList($page);
        // On compte le nombre page pour 10 articles par pages
        $countPages=ceil($articlesCount/10);
            return $this->render('article/index.html.twig', array(
            'articles' => $articles,'dernierePage'=>$countPages,'currentPage'=>$page,'searchForm'=>$formSearch->createView()
        ));
        }
    }

    /**
     * Affichage d'un article
     *
     * @Route("/article/{id}", name="article_show")
     */
    public function showArticleAction(Request $request, Article $article)
    {
        // On récupère les commentaires de l'article
        $comments=$article->getComments();
        // On créer le formulaire de commentaires
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

    /**
    * Affichage des articles par catégorie
     * @Route("/categorie/{name}/{page}", name="category")
     */
    public function articlesByCategoryAction(Request $request, $name, $page=1)
    {
        // Formulaire de recherche par nom et/ou tags d'article de la catégorie
      $formSearch=$this->createForm(SearchArticleType::class);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // On récupère les données du formulaire
        $data=$request->request->get("search_article");

            if (array_key_exists('name', $data)) {
                $nameArticle = $data['name'];
            } else {
                $nameArticle ="";
            }
        // Si aucun tag n'est sélectionné on les récupère tous pour ne pas fausser la recherche

        if (array_key_exists('tags', $data)) {
            $tags = $data['tags'];
        } else {
            $tags=$this->getDoctrine()->getRepository('AppBundle:Tag')->findAll();
        }

        // On affiche les articles correspondants à la recherche sans pagination
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->getArticlesBySearchCategory($name, $tags, $nameArticle);

            return $this->render('article/index.html.twig', array(
            'articles' => $articles,'searchForm'=>$formSearch->createView()
        ));
        } else {
            $articlesCount = $this->getDoctrine()->getRepository('AppBundle:Article')->getCountArticlesOnlineByCategory($name);

            $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->getArticlesByCategory($name, $page);
            $countPages=ceil($articlesCount/10);

            return $this->render('article/index.html.twig', array(
            'articles' => $articles,'dernierePage'=>$countPages,'currentPage'=>$page,'searchForm'=>$formSearch->createView()
        ));
        }
    }
}
