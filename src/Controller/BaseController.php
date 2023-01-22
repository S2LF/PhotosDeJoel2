<?php

namespace App\Controller;

use App\Repository\BaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
  protected $base;

  public function __construct(BaseRepository $BaseRepository)
  {
    $base = $BaseRepository->findOneBy(['id' => 1]);

    if ($base == null) {
      $base = [
        "siteTitle" => "Titre par défaut",
        "headerContent" => "Texte à écrire par défaut",
        "homepageWord" => "Mot page d'accueil par défaut",
        "homepageImagePath" => null,
        "textFooter" => "texte pied de page par défaut"
      ];
    }
    $this->base = $base;
  }
}
