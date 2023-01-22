<?php

namespace App\Controller;

use App\Repository\LinkRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends BaseController
{

  #[Route(path: '/liens', name: 'links')]
  public function index(LinkRepository $lienrepo): Response
  {
    $links =  $lienrepo->findBy([], ['position' => 'ASC']);

    return $this->render('link/index.html.twig', [
      'base' => $this->base,
      'links' => $links
    ]);
  }
}
