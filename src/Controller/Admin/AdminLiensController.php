<?php

namespace App\Controller\Admin;

use App\Entity\Lien;
use App\Form\LienType;
use App\Repository\LienRepository;
use App\Service\FileUploaderService;
use App\Controller\GeneralController;
use App\Repository\GeneralRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;

#[Route(path: '/admin', IsGranted: 'ROLE_ADMIN')]
class AdminLiensController extends GeneralController
{
  #[Route(path: '/lien', name: 'admin_lien')]
  public function index(LienRepository $lrepo)
  {
    $liens = $lrepo->findAllOrderByPos();

    return $this->render('admin/liens/index.html.twig', [
      'general' => $this->general,
      'liens' => $liens
    ]);
  }

  #[Route(path: '/lien/add', name: 'admin_lien_add')]
  #[Route(path: '/lien/edit/{id}', name: 'admin_lien_edit')]
  public function formCat(Lien $lien = null, Request $request, EntityManagerInterface $em)
  {
    if (!$lien) {
      $lien = new Lien;
    }

    $form = $this->createForm(LienType::class, $lien);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($lien);
      $em->flush();
      $this->addFlash("success", "Le lien a bien été ajouté/modifié");
      return $this->redirectToRoute('admin_lien');
    }

    return $this->render('admin/liens/addLien.html.twig', [
      'form' => $form->createView(),
      'general' => $this->general,
    ]);
  }

  #[Route(path: '/lien/sort', name: 'admin_lien_sort')]
  public function sortableLien(Request $request, EntityManagerInterface $em, LienRepository $lrepo)
  {

    $lien_id = $request->request->get('lien_id');
    $position = $request->request->get('position');

    $lien = $lrepo->findOneBy(['id' => $lien_id]);

    $lien->setPosition($position);

    try {
      $em->flush();
      return new Response(true);
    } catch (\PdoException $e) {
    }
  }

  #[Route(path: '/liens/delete/{id}', name: 'admin_lien_delete')]
  public function deleteActu(Lien $lien = null, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
  {
    if (!$lien) {
      throw new NotFoundException('Lien non trouvé');
    }

    $em->remove($lien);
    $em->flush();

    $this->addFlash("success", "Le lien a bien été supprimé");

    return $this->redirectToRoute('admin_lien');
  }
}
