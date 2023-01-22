<?php

namespace App\Controller;

use App\Repository\GeneralRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GeneralController extends AbstractController
{
  protected $general;

  public function __construct(GeneralRepository $generalRepo)
  {
    $general = $generalRepo->findOneBy(['id' => 1]);
    if ($general == null) {
      $general = [
        "titreDuSiteHeader" => "Titre par défaut",
        "texteHeader" => "Texte à écrire par défaut",
        "motPageAccueil" => "Mot page d'accueil par défaut",
        "photoAccueilPath" => null,
        "textFooter" => "texte pied de page par défaut"
      ];
    }
    $this->general = $general;
  }
}
