<?php

namespace App\Form;

use App\Entity\Mailer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NoReplyEmail', TextType::class, [
                'label' => 'Email de no-reply*:'
            ])
            ->add('adminEmail', TextType::class, [
                'label' => 'Email admin*:'
            ])
            ->add('rgpdText', TextType::class, [
                'label' => 'Texte RGPD*:'
            ])
            ->add('emailSubject', TextType::class, [
                'label' => 'Objet du mail*:'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mailer::class,
        ]);
    }
}
