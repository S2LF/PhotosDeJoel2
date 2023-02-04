<?php

namespace App\Controller;

use App\Repository\ActualityRepository;
use App\Repository\BaseRepository;
use App\Repository\CategoryPhotoRepository;
use App\Repository\ExpositionRepository;
use App\Repository\LinkRepository;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected $base;
    protected $randomImagePath;
    protected $expositionsCount;
    protected $linksCount;
    protected $actusCount;
    protected $categoriesCount;

    public function __construct(
        BaseRepository $baseRepository,
        PhotoRepository $photoRepository,
        CategoryPhotoRepository $categoryPhotoRepository,
        ExpositionRepository $expositionRepository,
        LinkRepository $linkRepository,
        ActualityRepository $actualityRepository
    ) {
        $base = $baseRepository->findOneBy(['id' => 1]);

        if ($base == null) {
            $base = [
                "siteTitle" => "Titre par défaut",
                "headerContent" => "Texte à écrire par défaut",
                "homepageWord" => "Mot page d'accueil par défaut",
                "homepageImagePath" => null,
                "textFooter" => "texte pied de page par défaut"
            ];
        }

        // Get random image for homepage
        $photos = $photoRepository->findAll();

        if($photos == null) {
            $randomPhoto = [
                "path" => null
            ];
            $this->randomImagePath = $randomPhoto['path'];
        } else {
            $randomPhoto = $photos[array_rand($photos)];
            $this->randomImagePath = $randomPhoto->getPath();
        }

        $this->base = $base;
        $this->expositionsCount = $expositionRepository->count(['deletedAt' => null]);
        $this->linksCount = $linkRepository->count(['deletedAt' => null]);
        $this->actusCount = $actualityRepository->count(['deletedAt' => null]);
        $this->categoriesCount = $categoryPhotoRepository->count(['deletedAt' => null]);
    }
}
