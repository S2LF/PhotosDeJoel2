<?php

namespace App\Controller;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends BaseController
{

  #[Route(path: '/contact', name: 'contact')]
  public function index(Request $request, MailerInterface $mailer): Response
  {
    $contactForm = $this->createFormBuilder()
      ->add('captcha', Recaptcha3Type::class, [
        'constraints' => new Recaptcha3 ([
            'message' => 'karser_recaptcha3.message',
            'messageMissingValue' => 'karser_recaptcha3.message_missing_value',
        ]),
      ])
      ->add('name', TextType::class, [
        'label' => 'Votre nom',
        'attr' => [
          'maxlength' => 30
        ],
      ])
      ->add('email', EmailType::class, [
        'label' => 'Votre e-mail',
        'attr' => [
          'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$',
          'match' => false,
          'message' => 'E-mail incomplet blablabla',
          'placeholder' => 'email@domaine.fr'
        ]
      ])
      ->add('subject', TextType::class, [
        'label' => 'Sujet',
        'attr' => [
          'maxlength' => 30
        ]
      ])
      ->add('content', TextareaType::class, [
        'label' => 'Votre message'
      ])
      ->add('rgpd', CheckboxType::class, [
        'label' => 'En cochant cette case et en soumettant ce formulaire, j’accepte que mes données personnelles soient utilisées pour me recontacter dans le cadre de ma demande. Aucun autre traitement ne sera effectué avec mes informations.'
      ])
      ->add('submit', SubmitType::class, [
        'label' => 'Envoyer'
      ])
      ->getForm();


    $contactForm->handleRequest($request);
    if ($contactForm->isSubmitted() && $contactForm->isValid()) {
      $data = $contactForm->getData();

      $template = '
            <p>
                <b>Objet :</b> ' . $data['subject'] . '
            </p>
            <p>
                <b>Nom :</b> ' . $data['name'] . '<br>
                <b>Email :</b> <a href="mailto:' . $data['email'] . '">' . $data['email'] . '</a>
            </p>
            <p>
                <b>Message :</b><br>
                ' . $data['content'] . '
            </p>
            <hr>
            <p>
                Ce message a été envoyé via le formulaire de contact du site Les Photos de Joël
            </p>
            ';

      $email = (new Email())
        ->from('no-reply@photodejoel.fr')
        ->to('joel.allain@gmx.fr')
        ->subject('Formulaire de contact: ' . $data['subject'])
        ->html($template);

      $mailer->send($email);

      return $this->redirectToRoute('contact');
    }

    return $this->render('contact/index.html.twig', [
      'base' => $this->base,
      'expositonsCount' => $this->expositionsCount,
      'linksCount' => $this->linksCount,
      'actusCount' => $this->actusCount,
      'categoriesCount' => $this->categoriesCount,
      'contactForm' => $contactForm->createView()
    ]);
  }
}
