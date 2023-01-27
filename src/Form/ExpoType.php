<?php

namespace App\Form;

use App\Entity\Exposition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExpoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('title', TextType::class, [
            'label' => 'Titre*:'
          ])
          ->add('eventDate', DateTimeType::class, [
            'label' => "Date de l'évènement"
          ])
          ->add('place', TextType::class, [
            'label' => 'Lieu:',
            'required' => false
          ])
          ->add('path', FileType::class, [
            'label' => 'Image de l\'expo (JPG/PNG/GIF, max 2Mo)*',

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
          ->add('content', TextareaType::class, [
            'label' => 'Description:',
            'required' => false
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => Exposition::class,
        ]);
    }
}
