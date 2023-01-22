<?php

namespace App\Controller\Admin;

use App\Entity\base;
use App\Form\baseType;
use App\Service\FileUploaderService;
use App\Controller\BaseController;
use App\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route(path: '/admin', IsGranted: 'ROLE_ADMIN')]
class AdminController extends BaseController
{
  /**
   * @Route("/", name="admin")
   */
  #[Route(path: '/', name: 'admin')]
  public function index()
  {
    return $this->redirectToRoute('admin_base');
  }

  /**
   * @Route("/base", name="admin_base")
   */
  #[Route(path: '/base', name: 'admin_base')]
  public function base(Request $request, EntityManagerInterface $em, BaseRepository $grepo, FileUploaderService $fileUploaderService)
  {
    $baseForm = $grepo->findOneBy(['id' => 1]);
    if (!$baseForm) {
      $baseForm = new base;
    }

    $form = $this->createForm(baseType::class, $baseForm);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if ($imageFile = $form->get("photo_accueil_path")->getData()) {
        $newFilename = "home";
        $directory = "/base/";
        $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
        $baseForm->setHomepageImagePath($directory . "/" . $imageFileName);
      }

      $em->persist($baseForm);
      $em->flush();
      $this->addFlash("success", "Les informations ont bien été modifiés");
      return $this->redirectToRoute('admin');
    }

    return $this->render('admin/base.html.twig', [
      'base' => $this->base,
      "form" => $form->createView()
    ]);
  }
}
