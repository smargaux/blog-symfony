<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\RegistrationType;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
      $authenticationUtils = $this->get('security.authentication_utils');

      // get the login error if there is one
      $error = $authenticationUtils->getLastAuthenticationError();

      // last username entered by the user
      $lastUsername = $authenticationUtils->getLastUsername();

      return $this->render('login.html.twig', array(
          'last_username' => $lastUsername,
          'error'         => $error,
      ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
    }
  /**
   * @Route("/connecte", name="login_check")
   */
   public function loginCheckAction(Request $request)
   {
   }
   /**
    * @Route("/inscription", name="register")
    */
   public function registerAction(Request $request)
    {
      $user = new User();
      $form = $this->createForm(RegistrationType::class, $user);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {

          $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
          $user->setPassword($password);
          $em = $this->getDoctrine()->getManager();
          $em->persist($user);
          $em->flush();

          return $this->redirectToRoute('administration');
      }

      return $this->render(
          'registration.html.twig',
          array('form' => $form->createView())
      );
    }

}
