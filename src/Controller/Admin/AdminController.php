<?php

namespace App\Controller\Admin;

use App\Entity\General;
use App\Form\GeneralType;
use App\Service\FileUploaderService;
use App\Controller\GeneralController;
use App\Repository\GeneralRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route(path: '/admin', IsGranted: 'ROLE_ADMIN')]
class AdminController extends GeneralController
{
  /**
   * @Route("/", name="admin")
   */
  #[Route(path: '/', name: 'admin')]
  public function index()
  {
    return $this->redirectToRoute('admin_general');
  }

  /**
   * @Route("/general", name="admin_general")
   */
  #[Route(path: '/general', name: 'admin_general')]
  public function general(Request $request, EntityManagerInterface $em, GeneralRepository $grepo, FileUploaderService $fileUploaderService)
  {
    $generalForm = $grepo->findOneBy(['id' => 1]);
    if (!$generalForm) {
      $generalForm = new General;
    }

    $form = $this->createForm(GeneralType::class, $generalForm);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if ($imageFile = $form->get("photo_accueil_path")->getData()) {
        $newFilename = "home";
        $directory = "/general/";
        $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
        $generalForm->setPhotoAccueilPath($directory . "/" . $imageFileName);
      }

      $em->persist($generalForm);
      $em->flush();
      $this->addFlash("success", "Les informations ont bien été modifiés");
      return $this->redirectToRoute('admin');
    }

    return $this->render('admin/general.html.twig', [
      'general' => $this->general,
      "form" => $form->createView()
    ]);
  }
}
