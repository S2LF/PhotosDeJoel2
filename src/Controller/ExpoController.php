<?php

namespace App\Controller;

use App\Repository\ExpoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpoController extends GeneralController
{
  /**
   * @Route("/expositions", name="expo")
   */
  public function index(ExpoRepository $exporepo): Response
  {
    $expos =  $exporepo->findBy([], ['position' => 'ASC']);

    return $this->render('expo/index.html.twig', [
      'general' => $this->general,
      'expos' => $expos
    ]);
  }
}
