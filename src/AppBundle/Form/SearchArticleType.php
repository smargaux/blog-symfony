<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('name',TextType::class,array('required'=>false))
          ->add('tags',EntityType::class, array(
              'class' => 'AppBundle:Tag',
              'choice_label' => 'name',
              'required'=>false,
              'multiple' => true,
              'expanded' => true,) )
              ->add('submit',SubmitType::class);

    }


}
