<?php

namespace App\Controller;

use App\Entity\PhotoCategorie;
use App\Repository\PhotoRepository;
use App\Repository\PhotoCategorieRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class PhotoController extends GeneralController
{
  /**
   * @Route("/", name="cats")
   */
  public function index(PhotoCategorieRepository $pcrepo)
  {
    $cats =  $pcrepo->findBy([], ['position' => 'ASC']);

    return $this->render('photos/index.html.twig', [
      'general' => $this->general,
      'cats' => $cats
    ]);
  }

  /**
   * @Route("/{id}/photos", name="photo")
   */
  public function photo_cat(PhotoCategorie $cat, PhotoRepository $prepo)
  {
    $photos = $prepo->findBy(['photo_categorie' => $cat], ['position' => 'ASC']);

    return $this->render('photos/catPhotos.html.twig', [
      'general' => $this->general,
      'cat' => $cat,
      'photos' => $photos
    ]);
  }
}
