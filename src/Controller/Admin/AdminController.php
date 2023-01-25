<?php

namespace App\Controller\Admin;

use App\Entity\base;
use App\Form\BaseType;
use App\Service\FileUploaderService;
use App\Controller\BaseController;
use App\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends BaseController
{

  #[Route(path: '/', name: 'admin')]
  public function index(Request $request, EntityManagerInterface $em, BaseRepository $grepo, FileUploaderService $fileUploaderService)
  {
    $baseForm = $grepo->findOneBy(['id' => 1]);
    if (!$baseForm) {
      $baseForm = new base;
    }

    $form = $this->createForm(BaseType::class, $baseForm);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if ($imageFile = $form->get("homepageImagePath")->getData()) {
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
      'expositonsCount' => $this->expositionsCount,
      'linksCount' => $this->linksCount,
      'actusCount' => $this->actusCount,
      'categoriesCount' => $this->categoriesCount,
      "form" => $form->createView()
    ]);
  }
}
