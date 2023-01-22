<?php

namespace App\Form;

use App\Entity\Lien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LienType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('titre', TextType::class, [
        'label' => 'Titre*:'
      ])
      ->add('lien', TextType::class, [
        'label' => 'Lien*:'
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Lien::class,
    ]);
  }
}
