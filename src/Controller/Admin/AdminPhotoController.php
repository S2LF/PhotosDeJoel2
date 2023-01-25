<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\CategoryPhoto;
use App\Entity\Photo;
use App\Form\PhotoType;
use App\Form\PhotoEditType;
use App\Repository\PhotoRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminPhotoController extends BaseController
{
  #[Route(path: '/cat/{id}/photos', name: 'admin_cat_photos')]
  public function index(CategoryPhoto $cat, PhotoRepository $prepo)
  {
    $photos = $prepo->findBy(['categoryPhoto' => $cat, 'deletedAt' => null], ['position' => 'ASC']);

    return $this->render('admin/photos/catPhotos.html.twig', [
      'base' => $this->base,
      'expositonsCount' => $this->expositionsCount,
      'linksCount' => $this->linksCount,
      'actusCount' => $this->actusCount,
      'categoriesCount' => $this->categoriesCount,
      'cat' => $cat,
      'photos' => $photos
    ]);
  }

  #[Route(path: '/cat/{id}/addPhoto', name: 'admin_add_photo')]
  public function addPhoto(CategoryPhoto $cat, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    $photo = new Photo;
    $form = $this->createForm(PhotoType::class, $photo);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if ($imageFile = $form->get("path")->getData()) {
        $exif = @\exif_read_data($imageFile);

        if ($exif) {

          $wanted = ["Model" => "", "ExposureTime" => "", "FNumber" => "", "ISOSpeedRatings" => "", "FocalLength" => ""];
          $new_exif = array_intersect_key($exif, $wanted);

          // CHANGE KEY
          // $arr[$newkey] = $arr[$oldkey];
          // unset($arr[$oldkey]);

          $photo->setExifs($new_exif);
        }

        $newFilename = $photo->getTitle();
        $directory = "/photo/" . $cat->getId() . "/photo";
        $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
        $photo->setPath($directory . "/" . $imageFileName);
        $photo->setCategoryPhoto($cat);
      }

      $em->persist($photo);
      $em->flush();
      $this->addFlash("success", "La photo a bien été ajoutée");
      return $this->redirectToRoute('admin_cat_photos', [
        'id' => $cat->getId()
      ]);
    }

    return $this->render('admin/photos/addPhoto.html.twig', [
      'base' => $this->base,
      'expositonsCount' => $this->expositionsCount,
      'linksCount' => $this->linksCount,
      'actusCount' => $this->actusCount,
      'categoriesCount' => $this->categoriesCount,
      'form' => $form->createView()
    ]);
  }

  #[Route(path: '/cat/{cat_id}/modifier-photo/{photo_id}', name: 'admin_edit_photo')]
  #[Entity('cat', expr: 'repository.find(cat_id)')]
  #[Entity('photo', expr: 'repository.find(photo_id)')]
  public function editPhoto(CategoryPhoto $cat, Photo $photo, Request $request, EntityManagerInterface $em)
  {
    $form = $this->createForm(PhotoEditType::class, $photo);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $em->flush();
      $this->addFlash("success", "La photo a bien été modifiée");
      return $this->redirectToRoute('admin_cat_photos', [
        'id' => $cat->getId()
      ]);
    }

    return $this->render('admin/photos/addPhoto.html.twig', [
      'base' => $this->base,
      'expositonsCount' => $this->expositionsCount,
      'linksCount' => $this->linksCount,
      'actusCount' => $this->actusCount,
      'categoriesCount' => $this->categoriesCount,
      'photo' => $photo,
      'form' => $form->createView()
    ]);
  }

  #[Route(path: '/photo/sort', name: 'admin_photo_sort')]
  public function sortablePhoto(Request $request, EntityManagerInterface $em, PhotoRepository $prepo)
  {
    $photo_id = $request->request->get('photo_id');
    $position = $request->request->get('position');

    $photo = $prepo->findOneBy(['id' => $photo_id]);

    $photo->setPosition($position);

    try {
      $em->flush();
      return new Response(true);
    } catch (\PdoException $e) {
    }
  }

  #[Route(path: '/photo/delete/{id}/{hardDelete}', name: 'admin_delete_photo')]
  public function deletePhoto(Photo $photo, $hardDelete = 0, EntityManagerInterface $em, PhotoRepository $prepo, FileUploaderService $fileUploaderService)
  {
    $cat = $photo->getCategoryPhoto();

    if($hardDelete) {
      try {
        if ($photo->getPath() !== null) {
          $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $photo->getPath());
        }
    
        $em->remove($photo);
        $em->flush();

        $this->addFlash("success", "La photo a bien été supprimé définitivement");
      } catch (Error $e) {
        return $this->addFlash("danger", "Une erreur est survenue, la photo n'a pas pu être supprimé");
      }
    } else {
      // Soft delete
      $prepo->remove($photo);

      $this->addFlash("success", "La photo a bien été supprimée");
    }

    return $this->redirectToRoute('admin_cat_photos', ['id' => $cat->getId()]);
  }
}
