<?php

namespace App\Controller;

use App\Entity\CategoryPhoto;
use App\Repository\CategoryPhotoRepository;
use App\Repository\PhotoRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/categories')]
class PhotoController extends BaseController
{
    #[Route(path: '/', name: 'cats')]
    public function index(CategoryPhotoRepository $pcrepo)
    {
        $cats =  $pcrepo->findAllOrderByPos();

        return $this->render('photos/index.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'cats' => $cats
        ]);
    }

    #[Route(path: '/{id}/photos', name: 'photo')]
    public function photo_cat(CategoryPhoto $cat, PhotoRepository $prepo)
    {
        $photos = $prepo->findAllOrderByPos();

        return $this->render('photos/catPhotos.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'cat' => $cat,
          'photos' => $photos,
        ]);
    }
}
