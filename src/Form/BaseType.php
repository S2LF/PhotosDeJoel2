<?php

namespace App\Form;

use App\Entity\base;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BaseType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('siteTitle', TextType::class, [
        'label' => 'Titre du site* :'
      ])
      ->add('headerContent', CKEditorType::class, [
        'label' => 'Texte en-tête* :'
      ])
      ->add('homepageWord', CKEditorType::class, [
        'label' => "Texte page d'accueil* :"
      ])
      ->add('homepageImagePath', FileType::class, [
        'label' => 'Photo de couverture (JPG/PNG/GIF, max 1Mo)',

        // Unmapped because not associated to any entity property
        'mapped' => false,

        // make it optional so you don't have to re-upload the PDF file
        // every time you edit the Product details
        'required' => false,

        // unmapped fields can't define their validation using annotations
        // in the associated entity, so you can use the PHP constraint classes
        'constraints' => [
          new File([
            'maxSize' => '5048k',
            'mimeTypes' => [
              'image/jpeg',
              'image/png',
              'image/gif'
            ],
            'mimeTypesMessage' => 'Veuillez respecter les restrictions de taille et de format',
          ])
        ]
      ])
      ->add('text_footer', TextType::class, [
        'label' => 'Texte pied de page*:'
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => base::class,
    ]);
  }
}
