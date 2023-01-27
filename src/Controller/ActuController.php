<?php

namespace App\Controller;

use App\Repository\ActualityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActuController extends BaseController
{

  #[Route(path: '/actualites', name: 'actu')]
  public function index(ActualityRepository $acturepo): Response
  {
    $actus =  $acturepo->findAllOrderByPos();

    return $this->render('actu/index.html.twig', [
      'base' => $this->base,
      'expositonsCount' => $this->expositionsCount,
      'linksCount' => $this->linksCount,
      'actusCount' => $this->actusCount,
      'categoriesCount' => $this->categoriesCount,
      'actus' => $actus
    ]);
  }
}
