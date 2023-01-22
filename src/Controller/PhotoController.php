<?php

namespace App\Controller;

use App\Entity\CategoryPhoto;
use App\Repository\CategoryPhotoRepository;
use App\Repository\PhotoRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/category')]
class PhotoController extends BaseController
{

  #[Route(path: '/', name: 'cats')]
  public function index(CategoryPhotoRepository $pcrepo)
  {
    $cats =  $pcrepo->findBy([], ['position' => 'ASC']);

    return $this->render('photos/index.html.twig', [
      'base' => $this->base,
      'cats' => $cats
    ]);
  }

  #[Route(path: '/{id}/photos', name: 'photo')]
  public function photo_cat(CategoryPhoto $cat, PhotoRepository $prepo)
  {
    $photos = $prepo->findBy(['photo_categorie' => $cat], ['position' => 'ASC']);

    return $this->render('photos/catPhotos.html.twig', [
      'base' => $this->base,
      'cat' => $cat,
      'photos' => $photos
    ]);
  }
}
