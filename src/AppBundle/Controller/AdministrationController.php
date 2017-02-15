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
      // Affichage de la page d'accueil de l'administration
      return $this->render('administration/index.html.twig');
  }

  /**
   * Liste de tous les articles existants
   *
   * @Route("/articles", name="article_index")
   * @Method("GET")
   */
  public function listArticlesAction()
  {
      $em = $this->getDoctrine()->getManager();
      // On récupère tous les articles (mêmes ceux non publiés)
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
        // On renomme la photo avec un nom unique
        $fileName = $article->getName().date('Y-m-d').'.'.$file->guessExtension();

        // On déplace le fichier de le dossier 'images-articles'
        $file->move(
            $this->getParameter('images_directory'),
            $fileName
        );

        // On ajoute l'image à l'article
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
    $oldImageName = $article->getImage();

    $editForm = $this->createForm('AppBundle\Form\ArticleType', $article);
    $editForm->handleRequest($request);
    if ($editForm->isSubmitted() && $editForm->isValid()) {
        $article->setAuthor($this->getUser());
        // On charge l'ancienne image si aucune n'a été ajoutée dans la modification du formulaire
        if ($article->getImage()) {
            if ($oldImageName) {
                unlink($this->getParameter('images_directory').'/'.$oldImageName);
            }
            $file = $article->getImage();
            $fileName = $article->getName().date('Y-m-d').'.'.$file->guessExtension();

            $file->move(
                  $this->getParameter('images_directory'),
                  $fileName
              );

            $article->setImage($fileName);
        } else {
            $article->setImage($oldImageName);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('article_show', array('id' => $article->getId()));
    }

    return $this->render('article/edit.html.twig', array(
        'article' => $article,
        'edit_form' => $editForm->createView(),

    ));
}

/**
 * Suppression d'un article
 *
 * @Route("/article/{id}/delete", name="article_delete")
 */
public function deleteArticleAction(Request $request, Article $article)
{
    $em = $this->getDoctrine()->getManager();
    $em->remove($article);
    $em->flush($article);

    return $this->redirectToRoute('article_list');
}
/**
 * Liste de toutes les catégories
 *
 * @Route("/category/", name="category_index")
 * @Method("GET")
 */
public function indexCategoriesAction()
{
    $em = $this->getDoctrine()->getManager();

    $categories = $em->getRepository('AppBundle:Category')->findAll();

    return $this->render('category/index.html.twig', array(
        'categories' => $categories,
    ));
}
/**
 * Créer une nouvelle catégorie
 *
 * @Route("/category/new", name="category_new")
 * @Method({"GET", "POST"})
 */
public function newCategoryAction(Request $request)
{
    $category = new Category();
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush($category);
        return $this->redirectToRoute('category_index');
    }

    return $this->render('category/new.html.twig', array(
        'category' => $category,
        'form' => $form->createView(),
    ));
}
/**
 * Modifier une catégorie
 *
 * @Route("/category/{id}/edit", name="category_edit")
 * @Method({"GET", "POST"})
 */
public function editCategoryAction(Request $request, Category $category)
{
    $editForm = $this->createForm('AppBundle\Form\CategoryType', $category);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('category_index');
    }

    return $this->render('category/edit.html.twig', array(
        'category' => $category,
        'edit_form' => $editForm->createView()
    ));
}
/**
 * Suppression d'une catégorie
 *
 * @Route("/category/{id}/delete", name="category_delete")
 */
public function deleteCategoryAction(Request $request, Category $category)
{
    $em = $this->getDoctrine()->getManager();
    $em->remove($category);
    $em->flush($category);

    return $this->redirectToRoute('category_index');
}


/**
 * Liste de tous les tags
 *
 * @Route("/tags/", name="tag_index")
 * @Method("GET")
 */
public function indexTagsAction()
{
    $em = $this->getDoctrine()->getManager();

    $tags = $em->getRepository('AppBundle:Tag')->findAll();

    return $this->render('tag/index.html.twig', array(
        'tags' => $tags,
    ));
}

/**
 * Créer un nouveau tag
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

        return $this->redirectToRoute('tag_index');
    }

    return $this->render('tag/new.html.twig', array(
        'tag' => $tag,
        'form' => $form->createView(),
    ));
}

/**
 * Modifier un tag
 *
 * @Route("/tags/{id}/edit", name="tag_edit")
 * @Method({"GET", "POST"})
 */
public function editTagAction(Request $request, Tag $tag)
{
    $editForm = $this->createForm('AppBundle\Form\TagType', $tag);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('tag_edit', array('id' => $tag->getId()));
    }

    return $this->render('tag/edit.html.twig', array(
        'tag' => $tag,
        'edit_form' => $editForm->createView()
    ));
}

/**
 * Suppression d'un tag.
 *
 * @Route("/tag/{id}/delete", name="tag_delete")
 */
public function deleteTagAction(Request $request, Tag $tag)
{
    $em = $this->getDoctrine()->getManager();
    $em->remove($tag);
    $em->flush($tag);

    return $this->redirectToRoute('tag_index');
}
}
