<?php

namespace App\Controller;

use App\Repository\ExpositionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpoController extends BaseController
{

  #[Route(path: '/expositions', name: 'expo')]
  public function index(ExpositionRepository $exporepo): Response
  {
    $expos =  $exporepo->findBy([], ['position' => 'ASC']);

    return $this->render('expo/index.html.twig', [
      'base' => $this->base,
      'expos' => $expos
    ]);
  }
}
