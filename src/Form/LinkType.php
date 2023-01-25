<?php

namespace App\Form;

use App\Entity\Link;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LinkType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('title', TextType::class, [
        'label' => 'Titre*:'
      ])
      ->add('link', TextType::class, [
        'label' => 'Lien*:'
      ])
      ->add('content', CKEditorType::class, [
        'label' => 'Contenu*:'
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Link::class,
    ]);
  }
}
