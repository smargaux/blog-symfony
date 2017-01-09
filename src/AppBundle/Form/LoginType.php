<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class LoginType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
   {
       $builder
            // Max lenght : 200 chars
           ->add('username',TextType::class,array('required' => false))
           //Falcultative
           ->add('password',PasswordType::class,array('required' => false))

           ->add('Connexion', SubmitType::class)
       ;

   }
}
