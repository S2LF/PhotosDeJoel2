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
        $expos =  $exporepo->findAllOrderByPos();

        return $this->render('expo/index.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'expos' => $expos
        ]);
    }
}
