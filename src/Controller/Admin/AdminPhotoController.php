<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Form\PhotoEditType;
use App\Entity\PhotoCategorie;
use App\Repository\PhotoRepository;
use App\Service\FileUploaderService;
use App\Repository\GeneralRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PhotoCategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route(path: '/admin', IsGranted: 'ROLE_ADMIN')]
class AdminPhotoController extends AbstractController
{
  #[Route(path: '/cat/{id}/photos', name: 'admin_cat_photos')]
  public function index(PhotoCategorie $cat, GeneralRepository $grepo, PhotoCategorieRepository $pcrepo, PhotoRepository $prepo)
  {
    $general = $grepo->findOneBy(['id' => 1]);
    if ($general == null) {
      $general = [
        "titreDuSiteHeader" => "Titre par défaut",
        "texteHeader" => "Texte à écrire par défaut",
        "motPageAccueil" => "Mot page d'accueil par défaut",
        "photoAccueilPath" => null,
        "textFooter" => "texte pied de page par défaut"
      ];
    }

    $photos = $prepo->findBy(['photo_categorie' => $cat], ['position' => 'ASC']);

    return $this->render('admin/photos/catPhotos.html.twig', [
      'general' => $general,
      'cat' => $cat,
      'photos' => $photos
    ]);
  }

  #[Route(path: '/cat/{id}/addPhoto', name: 'admin_add_photo')]
  public function addPhoto(PhotoCategorie $cat, GeneralRepository $grepo, PhotoCategorieRepository $pcrepo, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    $general = $grepo->findOneBy(['id' => 1]);
    if ($general == null) {
      $general = [
        "titreDuSiteHeader" => "Titre par défaut",
        "texteHeader" => "Texte à écrire par défaut",
        "motPageAccueil" => "Mot page d'accueil par défaut",
        "photoAccueilPath" => null,
        "textFooter" => "texte pied de page par défaut"
      ];
    }

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

        $newFilename = $photo->getTitre();
        $directory = "/photo/" . $cat->getId() . "/photo";
        $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
        $photo->setPath($directory . "/" . $imageFileName);
        $photo->setPhotoCategorie($cat);
      }

      $em->persist($photo);
      $em->flush();
      $this->addFlash("success", "La photo a bien été ajoutée");
      return $this->redirectToRoute('admin_cat_photos', [
        'id' => $cat->getId()
      ]);
    }

    return $this->render('admin/photos/addPhoto.html.twig', [
      'general' => $general,
      'form' => $form->createView()
    ]);
  }

  // /**
  //  * @Route("/cat/{id_cat}/editPhoto/{id_photo}", name="admin_edit_photo")
  //  * @ParamConverter("cat", options={"mapping": {"id_cat" : "id"}})
  //  * @ParamConverter("photo", options={"mapping": {"id_photo" : "id"}})
  //  */
  #[Route(path: '/cat/{id_cat}/editPhoto/{id_photo}', name: 'admin_actu_sort')]
  // #[ParamConverter(path: '/actu/sort', name: 'admin_actu_sort')]
  // #[ParamConverter(path: '/actu/sort', name: 'admin_actu_sort')]
  public function editPhoto(PhotoCategorie $cat, Photo $photo, GeneralRepository $grepo, PhotoCategorieRepository $pcrepo, Request $request, FileUploaderService $fileUploaderService, EntityManagerInterface $em)
  {
    $general = $grepo->findOneBy(['id' => 1]);
    if ($general == null) {
      $general = [
        "titreDuSiteHeader" => "Titre par défaut",
        "texteHeader" => "Texte à écrire par défaut",
        "motPageAccueil" => "Mot page d'accueil par défaut",
        "photoAccueilPath" => null,
        "textFooter" => "texte pied de page par défaut"
      ];
    }

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
      'general' => $general,
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

  #[Route(path: '/photo/delete/{id}', name: 'admin_delete_photo')]
  public function deletePhoto(Photo $photo, EntityManagerInterface $em, PhotoRepository $prepo, FileUploaderService $fileUploaderService)
  {
    $cat = $photo->getPhotoCategorie();

    $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $photo->getPath());

    $em->remove($photo);
    $em->flush();

    $this->addFlash("success", "La photo a bien été supprimée");

    return $this->redirectToRoute('admin_cat_photos', ['id' => $cat->getId()]);
  }
}
