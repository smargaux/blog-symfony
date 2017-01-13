<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SearchArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('name')
          ->add('tag',EntityType::class, array(
              'class' => 'AppBundle:Tag',
              'choice_label' => 'name',
              'multiple' => true,
              'expanded' => true,) );
    }


}
