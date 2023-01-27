<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Mailer;
use App\Form\ContactType;
use App\Repository\MailerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminMailerController extends BaseController
{
    #[Route('/contact', name: 'admin_contact')]
    public function index(Request $request, EntityManagerInterface $em, MailerRepository $mailerRepository): Response
    {
      $contactForm = $mailerRepository->findOneBy(['id' => 1]);
      if (!$contactForm) {
        $contactForm = new Mailer;
      }

      $form = $this->createForm(ContactType::class, $contactForm);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($contactForm);
        $em->flush();
        $this->addFlash("success", "Les informations ont bien été modifiés");
        return $this->redirectToRoute('admin_contact');
      }

      return $this->render('admin/mailer/index.html.twig', [
        'base' => $this->base,
        'expositonsCount' => $this->expositionsCount,
        'linksCount' => $this->linksCount,
        'actusCount' => $this->actusCount,
        'categoriesCount' => $this->categoriesCount,
        "form" => $form->createView()
      ]);
    }
}
