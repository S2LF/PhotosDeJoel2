<?php

namespace App\Controller\Admin;

use App\Form\CatType;
use App\Entity\PhotoCategorie;
use App\Repository\PhotoRepository;
use App\Service\FileUploaderService;
use App\Controller\BaseController;
use App\Entity\CategoryPhoto;
use App\Repository\CategoryPhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminCatController extends BaseController
{

  #[Route(path: '/cat', name: 'admin_categories')]
  public function Index(CategoryPhotoRepository $pcrepo, PhotoRepository $prepo)
  {
    // $cats = $pcrepo->findAll();
    $cats = $pcrepo->findAllOrderByPos();

    return $this->render('admin/cats/index.html.twig', [
      'base' => $this->base,
      'cats' => $cats,
    ]);
  }

  #[Route(path: '/cat/editCat/{id}', name: 'admin_edit_cat')]
  #[Route(path: '/cat/addCat', name: 'admin_add_cat')]
  public function add_cat(CategoryPhoto $cat = null, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    if (!$cat) {
      $cat = new CategoryPhoto;
    }

    $form = $this->createForm(CatType::class, $cat);

    $form->handleRequest($request);

    if ($imageFile = $form->get("path")->getData()) {
      $newFilename = $cat->getTitle();
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
      'base' => $this->base,
      'cat' => $cat
    ]);
  }

  #[Route(path: '/cat/delete/{id}', name: 'admin_delete_cat')]
  public function deleteCat(CategoryPhoto $cat,EntityManagerInterface $em, PhotoRepository $prepo, FileUploaderService $fileUploaderService)
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
  public function sortableCat(Request $request, EntityManagerInterface $em, CategoryPhotoRepository $lrepo)
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
