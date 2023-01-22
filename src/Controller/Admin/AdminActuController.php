<?php

namespace App\Controller\Admin;

use App\Controller\GeneralController;
use App\Form\ActuType;
use App\Entity\Actualite;
use App\Service\FileUploaderService;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route(path: '/admin', IsGranted: 'ROLE_ADMIN')]
class AdminActuController extends GeneralController
{

  #[Route(path: '/actus', name: 'admin_actu')]
  public function index(ActualiteRepository $arepo)
  {
    $actus = $arepo->findAllOrderByPos();


    return $this->render('admin/actu/index.html.twig', [
      'general' => $this->general,
      'actus' => $actus
    ]);
  }

  #[Route(path: '/actu/add', name: 'admin_actu_add')]
  #[Route(path: '/actu/edit/{id}', name: 'admin_actu_edit')]
  public function formCat(Actualite $actu = null, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    if (!$actu) {
      $actu = new Actualite;
    }

    $form = $this->createForm(ActuType::class, $actu);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $actu->setDateCreation(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

      if ($imageFile = $form->get("path")->getData()) {
        $newFilename = $actu->getTitre();
      }

      $em->persist($actu);
      $em->flush();

      if ($imageFile = $form->get("path")->getData()) {
        $id = $actu->getId();
        $directory = "/actu/" . $id;
        $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
        $actu->setPath($directory . "/" . $imageFileName);

        $em->persist($actu);
        $em->flush();
      }

      $this->addFlash("success", "L'article d'actualité a bien été ajouté/modifié");
      return $this->redirectToRoute('admin_actu');
    }

    return $this->render('admin/actu/addActu.html.twig', [
      'form' => $form->createView(),
      'general' => $this->general,
    ]);
  }

  #[Route(path: '/actu/delete/{id}', name: 'admin_actu_delete')]
  public function deleteActu(Actualite $actu = null,EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    if (!$actu) {
      throw new NotFoundException('Actualité non trouvé');
    }

    if ($actu->getPath() !== null) {
      $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $actu->getPath());
    }

    $em->remove($actu);
    $em->flush();

    $this->addFlash("success", "L'actualité a bien été supprimé");

    return $this->redirectToRoute('admin_actu');
  }

  #[Route(path: '/actu/sort', name: 'admin_actu_sort')]
  public function sortableActu(Request $request, EntityManagerInterface $em, ActualiteRepository $arepo)
  {
    $actu_id = $request->request->get('actu_id');
    $position = $request->request->get('position');

    $actu = $arepo->findOneBy(['id' => $actu_id]);

    $actu->setPosition($position);

    try {
      $em->flush();
      return new Response(true);
    } catch (\PdoException $e) {
      throw new Error($e);
    }
  }
}
