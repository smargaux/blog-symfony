<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\TagType;
use AppBundle\Form\CategoryType;
use AppBundle\Form\ArticleType;

use AppBundle\Entity\Tag;
use AppBundle\Entity\Category;
use AppBundle\Entity\Article;

/**
 * Administration controller.
 *
 * @Route("administration")
 */
class AdministrationController extends Controller
{
    /**
   * @Route("/", name="administration")
   */
  public function indexAction(Request $request)
  {
      // replace this example code with whatever you need
      return $this->render('administration/index.html.twig', [
          'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
      ]);
  }

  /**
   * Liste de tous les articles existants
   *
   * @Route("/articles", name="article_index")
   * @Method("GET")
   */
  public function  listArticlesAction()
  {
      $em = $this->getDoctrine()->getManager();

      $articles = $em->getRepository('AppBundle:Article')->findAll();

      return $this->render('article/index.html.twig', array(
          'articles' => $articles,
      ));
  }
/**
 * Création d'un nouvel article
 *
 * @Route("/article/new", name="article_new")
 * @Method({"GET", "POST"})
 */
public function newArticleAction(Request $request)
{
    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // On récupère l'utilisateur connecté pour l'assigner en tant qu'auteur de l'article
        $article->setAuthor($this->getUser());
        $file = $article->getImage();
        // Generate a unique name for the file before saving it
        $fileName = $article->getName().date('Y-m-d').'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        $file->move(
            $this->getParameter('images_directory'),
            $fileName
        );

        // Update the 'brochure' property to store the PDF file name
        // instead of its contents
        $article->setImage($fileName);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush($article);

        return $this->redirectToRoute('article_show', array('id' => $article->getId()));
    }

    return $this->render('article/new.html.twig', array(
        'article' => $article,
        'form' => $form->createView(),
    ));
}
/**
 * Editer un article
 *
 * @Route("/articles/{id}/edit", name="article_edit")
 * @Method({"GET", "POST"})
 */
public function editAction(Request $request, Article $article)
{
    $editForm = $this->createForm('AppBundle\Form\ArticleType', $article);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $article->setAuthor($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('article_show', array('id' => $article->getId()));
    }

    return $this->render('article/edit.html.twig', array(
        'article' => $article,
        'edit_form' => $editForm->createView(),

    ));
}

/**
 * Creates a new category entity.
 *
 * @Route("/categorie/new", name="category_new")
 * @Method({"GET", "POST"})
 */
public function newCategorieAction(Request $request)
{
    $category = new Category();
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush($category);

        return $this->redirectToRoute('category_show', array('id' => $category->getId()));
    }

    return $this->render('category/new.html.twig', array(
        'category' => $category,
        'form' => $form->createView(),
    ));
}

/**
 * Creates a new tag entity.
 *
 * @Route("/tag/new", name="tag_new")
 * @Method({"GET", "POST"})
 */
public function newTagAction(Request $request)
{
    $tag = new Tag();
    $form = $this->createForm(TagType::class, $tag);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($tag);
        $em->flush($tag);

        return $this->redirectToRoute('tag_show', array('id' => $tag->getId()));
    }

    return $this->render('tag/new.html.twig', array(
        'tag' => $tag,
        'form' => $form->createView(),
    ));
}
}
