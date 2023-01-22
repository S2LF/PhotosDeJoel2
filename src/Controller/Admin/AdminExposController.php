<?php

namespace App\Controller\Admin;

use App\Entity\Expo;
use App\Form\ExpoType;
use App\Repository\ExpoRepository;
use App\Service\FileUploaderService;
use App\Controller\BaseController;
use App\Entity\Exposition;
use App\Repository\BaseRepository;
use App\Repository\ExpositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin', IsGranted: 'ROLE_ADMIN')]
class AdminExposController extends BaseController
{
  #[Route(path: '/expositions', name: 'admin_expos')]
  public function index(BaseRepository $grepo, ExpositionRepository $erepo)
  {
    $expos = $erepo->findAllOrderByPos();

    return $this->render('admin/expos/index.html.twig', [
      'base' => $this->base,
      'expos' => $expos
    ]);
  }

  #[Route(path: '/expositions/add', name: 'admin_expo_add')]
  #[Route(path: '/expositions/edit/{id}', name: 'admin_expo_edit')]
  public function formCat(Exposition $expo = null, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    if (!$expo) {
      $expo = new Exposition;
    }

    $form = $this->createForm(ExpoType::class, $expo);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $expo->setCreationDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

      if ($imageFile = $form->get("path")->getData()) {
        $newFilename = $expo->getTitle();
      }

      $em->persist($expo);
      $em->flush();

      if ($imageFile = $form->get("path")->getData()) {
        $id = $expo->getId();
        $directory = "/expo/" . $id;
        $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
        $expo->setPath($directory . "/" . $imageFileName);

        $em->persist($expo);
        $em->flush();
      }
      $this->addFlash("success", "L'exposition a bien été ajouté/modifié");
      return $this->redirectToRoute('admin_expos');
    }

    return $this->render('admin/expos/addExpo.html.twig', [
      'form' => $form->createView(),
      'base' => $this->base,
    ]);
  }

  #[Route(path: '/expositions/sort', name: 'admin_expo_sort')]
  public function sortableExpo(Request $request, EntityManagerInterface $em, ExpositionRepository $erepo)
  {
    $expo_id = $request->request->get('expo_id');
    $position = $request->request->get('position');

    $expo = $erepo->findOneBy(['id' => $expo_id]);

    $expo->setPosition($position);

    try {
      $em->flush();
      return new Response(true);
    } catch (\PdoException $e) {
    }
  }

  #[Route(path: '/expositions/delete/{id}', name: 'admin_expo_delete')]
  public function deleteActExpou(Exposition $expo = null, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    if (!$expo) {
      throw new NotFoundException('Exposition non trouvé');
    }

    if ($expo->getPath() !== null) {
      $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $expo->getPath());
    }

    $em->remove($expo);
    $em->flush();

    $this->addFlash("success", "L'exposition a bien été supprimé");

    return $this->redirectToRoute('admin_expos');
  }
}
