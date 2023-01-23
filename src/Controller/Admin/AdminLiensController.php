<?php

namespace App\Controller\Admin;

use App\Service\FileUploaderService;
use App\Controller\BaseController;
use App\Entity\Link;
use App\Form\LinkType;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route(path: '/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminLiensController extends BaseController
{
  #[Route(path: '/lien', name: 'admin_link')]
  public function index(LinkRepository $lrepo)
  {
    $links = $lrepo->findAllOrderByPos();

    return $this->render('admin/liens/index.html.twig', [
      'base' => $this->base,
      'liens' => $links
    ]);
  }

  #[Route(path: '/lien/add', name: 'admin_link_add')]
  #[Route(path: '/lien/edit/{id}', name: 'admin_link_edit')]
  public function formCat(Link $link = null, Request $request, EntityManagerInterface $em)
  {
    if (!$link) {
      $link = new Link;
    }

    $form = $this->createForm(LinkType::class, $link);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($link);
      $em->flush();
      $this->addFlash("success", "Le lien a bien été ajouté/modifié");
      return $this->redirectToRoute('admin_link');
    }

    return $this->render('admin/liens/addLien.html.twig', [
      'form' => $form->createView(),
      'base' => $this->base,
    ]);
  }

  #[Route(path: '/lien/sort', name: 'admin_link_sort')]
  public function sortableLien(Request $request, EntityManagerInterface $em, LinkRepository $lrepo)
  {

    $link_id = $request->request->get('link_id');
    $position = $request->request->get('position');

    $link = $lrepo->findOneBy(['id' => $link_id]);

    $link->setPosition($position);

    try {
      $em->flush();
      return new Response(true);
    } catch (\PdoException $e) {
    }
  }

  #[Route(path: '/liens/delete/{id}/{hardDelete}', name: 'admin_link_delete')]
  public function deleteLink(Link $link = null, $hardDelete = 0, EntityManagerInterface $em, FileUploaderService $fileUploaderService, LinkRepository $lrepo)
  {
    if (!$link) {
      throw new NotFoundException('Lien non trouvé');
    }

    if($hardDelete) {
      try {    
        $em->remove($link);
        $em->flush();

        $this->addFlash("success", "le lien a bien été supprimé définitivement");
      } catch (Error $e) {
        return $this->addFlash("danger", "Une erreur est survenue, le lien n'a pas pu être supprimé");
      }
    } else {
      // Soft delete
      $lrepo->remove($link);

      $this->addFlash("success", "le lien a bien été supprimé");
    }

    return $this->redirectToRoute('admin_link');
  }
}
