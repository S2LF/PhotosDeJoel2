<?php

namespace App\Controller\Admin;

use App\Form\CatType;
use App\Entity\PhotoCategorie;
use App\Repository\PhotoRepository;
use App\Service\FileUploaderService;
use App\Controller\GeneralController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PhotoCategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route(path: '/admin', IsGranted: 'ROLE_ADMIN')]
class AdminCatController extends GeneralController
{

  #[Route(path: '/cat', name: 'admin_categories')]
  public function Index(PhotoCategorieRepository $pcrepo, PhotoRepository $prepo)
  {
    // $cats = $pcrepo->findAll();
    $cats = $pcrepo->findAllOrderByPos();

    return $this->render('admin/cats/index.html.twig', [
      'general' => $this->general,
      'cats' => $cats,
    ]);
  }

  #[Route(path: '/cat/editCat/{id}', name: 'admin_edit_cat')]
  #[Route(path: '/cat/addCat', name: 'admin_add_cat')]
  public function add_cat(PhotoCategorie $cat = null, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    if (!$cat) {
      $cat = new PhotoCategorie;
    }

    $form = $this->createForm(CatType::class, $cat);

    $form->handleRequest($request);

    if ($imageFile = $form->get("path")->getData()) {
      $newFilename = $cat->getTitre();
    }

    if ($form->isSubmitted() && $form->isValid()) {
      if ($imageFile = $form->get("path")->getData()) {
        $directory = "/photo/cover";
        $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
        $cat->setPhotoCoverPath($directory . "/" . $imageFileName);
      }
      $em->persist($cat);
      $em->flush();

      $this->addFlash("success", "La catégorie a bien été crée/modifié");
      return $this->redirectToRoute('admin_categories');
    }

    return $this->render('admin/cats/addCat.html.twig', [
      'form' => $form->createView(),
      'general' => $this->general,
      'cat' => $cat
    ]);
  }

  #[Route(path: '/cat/delete/{id}', name: 'admin_delete_cat')]
  public function deleteCat(PhotoCategorie $cat,EntityManagerInterface $em, PhotoRepository $prepo, FileUploaderService $fileUploaderService)
  {
    $photos = $prepo->getPhotoCatByPos($cat->getId());

    $directory = "photo/" . $cat->getId();

    $fileUploaderService->deleteTree($fileUploaderService->getTargetDirectory() . $directory);
    $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $cat->getPhotoCoverPath());

    foreach($photos as $photo) {
      $em->remove($photo);
    } 

    $this->addFlash("success", "La catégorie, et les photos associés, ont bien été supprimés");

    $em->remove($cat);
    $em->flush();

    return $this->redirectToRoute('admin_categories');
  }

  #[Route(path: '/cat/sort', name: 'admin_cat_sort')]
  public function sortableCat(Request $request, EntityManagerInterface $em, PhotoCategorieRepository $lrepo)
  {
    $cat_id = $request->request->get('cat_id');
    $position = $request->request->get('position');

    $cat = $lrepo->findOneBy(['id' => $cat_id]);

    $cat->setPosition($position);

    try {
      $em->flush();
      return new Response(true);
    } catch (\PdoException $e) {
    }
  }
}
