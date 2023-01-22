<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActuController extends GeneralController
{

  #[Route(path: '/actualites', name: 'actu')]
  public function index(ActualiteRepository $acturepo): Response
  {
    $actus =  $acturepo->findBy([], ['position' => 'ASC']);

    return $this->render('actu/index.html.twig', [
      'general' => $this->general,
      'actus' => $actus
    ]);
  }
}
