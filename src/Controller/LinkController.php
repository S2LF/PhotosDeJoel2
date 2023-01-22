<?php

namespace App\Controller;

use App\Repository\LienRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends GeneralController
{

  #[Route(path: '/liens', name: 'links')]
  public function index(LienRepository $lienrepo): Response
  {
    $links =  $lienrepo->findBy([], ['position' => 'ASC']);

    return $this->render('link/index.html.twig', [
      'general' => $this->general,
      'links' => $links
    ]);
  }
}
