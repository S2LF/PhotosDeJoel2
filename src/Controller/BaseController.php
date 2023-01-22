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
        "titreDuSiteHeader" => "Titre par défaut",
        "texteHeader" => "Texte à écrire par défaut",
        "motPageAccueil" => "Mot page d'accueil par défaut",
        "photoAccueilPath" => null,
        "textFooter" => "texte pied de page par défaut"
      ];
    }
    $this->base = $base;
  }
}
