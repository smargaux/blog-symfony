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
    $oldImageName = $article->getImage();

    $editForm = $this->createForm('AppBundle\Form\ArticleType', $article);
    $editForm->handleRequest($request);
    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $article->setAuthor($this->getUser());
        // On charge l'ancienne image si aucune n'a été ajoutée dans la modification du formulaire
        if($article->getImage()) {
              if($oldImageName){
              unlink($this->getParameter('images_directory').'/'.$oldImageName);
            }
              $file = $article->getImage();
              $fileName = $article->getName().date('Y-m-d').'.'.$file->guessExtension();

              $file->move(
                  $this->getParameter('images_directory'),
                  $fileName
              );

              $article->setImage($fileName);
          }
          //else we keep the old image
          else {
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
public function newCategorieAction(Request $request)
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
    $deleteForm = $this->createDeleteForm($category);
    $editForm = $this->createForm('AppBundle\Form\CategoryType', $category);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('category_index');
    }

    return $this->render('category/edit.html.twig', array(
        'category' => $category,
        'edit_form' => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
    ));
}
/**
 * Deletes a category entity.
 *
 * @Route("/category/{id}", name="category_delete")
 * @Method("DELETE")
 */
public function deleteCategoryAction(Request $request, Category $category)
{
    $form = $this->createDeleteForm($category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush($category);
    }

    return $this->redirectToRoute('category_index');
}

/**
 * Creates a form to delete a category entity.
 *
 * @param Category $category The category entity
 *
 * @return \Symfony\Component\Form\Form The form
 */
private function createDeleteCategoryForm(Category $category)
{
    return $this->createFormBuilder()
        ->setAction($this->generateUrl('category_delete', array('id' => $category->getId())))
        ->setMethod('DELETE')
        ->getForm()
    ;
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
    $deleteForm = $this->createDeleteForm($tag);
    $editForm = $this->createForm('AppBundle\Form\TagType', $tag);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('tag_edit', array('id' => $tag->getId()));
    }

    return $this->render('tag/edit.html.twig', array(
        'tag' => $tag,
        'edit_form' => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
    ));
}

/**
 * Deletes a tag entity.
 *
 * @Route("/tag/{id}", name="tag_delete")
 * @Method("DELETE")
 */
public function deleteTagAction(Request $request, Tag $tag)
{
    $form = $this->createDeleteForm($tag);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tag);
        $em->flush($tag);
    }

    return $this->redirectToRoute('tag_index');
}

/**
 * Creates a form to delete a tag entity.
 *
 * @param Tag $tag The tag entity
 *
 * @return \Symfony\Component\Form\Form The form
 */
private function createDeleteTagForm(Tag $tag)
{
    return $this->createFormBuilder()
        ->setAction($this->generateUrl('tag_delete', array('id' => $tag->getId())))
        ->setMethod('DELETE')
        ->getForm()
    ;
}


}
