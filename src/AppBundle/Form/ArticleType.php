<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('name')
          ->add('content',CKEditorType::class, array(
'config' => array(
'uiColor' => '#ffffff',
)))
          ->add('category',EntityType::class, array(
              'class' => 'AppBundle:Category',
              'choice_label' => 'name',
              'multiple' => false,
              'expanded' => true,))
          ->add('tags',EntityType::class, array(
              'class' => 'AppBundle:Tag',
              'choice_label' => 'name',
              'multiple' => true,
              'expanded' => true,))
          ->add('publication_date',DateTimeType::class,array('widget' => 'single_text'))
          ->add('image', FileType::class, array('label' => 'Image à la une', 'data_class' => null
))
          ->add('submit',SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_article';
    }


}
