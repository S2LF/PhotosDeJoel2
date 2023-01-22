<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends GeneralController
{
  /**
   * @Route("/", name="home")
   */
  public function index()
  {
    return $this->render('home/index.html.twig', [
      'general' => $this->general,
    ]);
  }
}
