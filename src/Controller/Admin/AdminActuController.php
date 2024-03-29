<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Form\ActuType;
use App\Entity\Actuality;
use App\Service\FileUploaderService;
use App\Repository\ActualityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Liip\ImagineBundle\Exception\Config\Filter\NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/actualites')]
#[IsGranted('ROLE_ADMIN')]
class AdminActuController extends BaseController
{
    #[Route(path: '/', name: 'admin_actu')]
    public function index(ActualityRepository $arepo)
    {
        $actus = $arepo->findAllOrderByPos();
        $actusDeleted = $arepo->findAllOrderByPosDeleted();

        return $this->render('admin/actu/index.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'actus' => $actus,
          'actusDeleted' => $actusDeleted,
        ]);
    }

    #[Route(path: '/add', name: 'admin_actu_add')]
    #[Route(path: '/edit/{id}', name: 'admin_actu_edit')]
    public function formCat(Actuality $actu = null, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
    {
        if (!$actu) {
            $actu = new Actuality();
        }

        $form = $this->createForm(ActuType::class, $actu);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actu->setCreationDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            if ($imageFile = $form->get("path")->getData()) {
                $newFilename = $actu->getTitle();
            }

            $em->persist($actu);
            $em->flush();

            if ($imageFile = $form->get("path")->getData()) {
                $id = $actu->getId();
                $directory = "/actu/" . $id;
                $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
                $actu->setPath($directory . "/" . $imageFileName);

                $em->persist($actu);
                $em->flush();
            }

            $this->addFlash("success", "L'article d'actualité a bien été ajouté/modifié");
            return $this->redirectToRoute('admin_actu');
        }

        return $this->render('admin/actu/addActu.html.twig', [
          'form' => $form->createView(),
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
        ]);
    }

    #[Route(path: '/delete/{id}/{hardDelete}', name: 'admin_actu_delete')]
    public function deleteActu(Actuality $actu = null, $hardDelete = 0, EntityManagerInterface $em, FileUploaderService $fileUploaderService, ActualityRepository $arepo)
    {
        if (!$actu) {
            throw new NotFoundException('Actualité non trouvé');
        }

        if ($hardDelete) {
            try {
                $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $actu->getPath());

                $em->remove($actu);
                $em->flush();

                $this->addFlash("success", "L'actualité a bien été supprimé définitivement");
            } catch (Error $e) {
                $this->addFlash("danger", "Une erreur est survenue, l'actualité n'a pas pu être supprimé");
            }
        } else {
            // Soft delete
            $arepo->remove($actu);

            $this->addFlash("success", "L'actualité a bien été supprimé");
        }

        return $this->redirectToRoute('admin_actu');
    }

    #[Route(path: '/sort', name: 'admin_actu_sort')]
    public function sortableActu(Request $request, EntityManagerInterface $em, ActualityRepository $arepo)
    {
        $actu_id = $request->request->get('actu_id');
        $position = $request->request->get('position');

        $actu = $arepo->findOneBy(['id' => $actu_id]);

        $actu->setPosition($position);

        try {
            $em->flush();
            return new Response(true);
        } catch (\PdoException $e) {
            throw new Error($e);
        }
    }

    #[Route(path: '/restore/{id}', name: 'admin_actu_restore')]
    public function restoreActu(Actuality $actu = null, EntityManagerInterface $em)
    {
        if (!$actu) {
            throw new NotFoundException('Actualité non trouvé');
        }

        $actu->setDeletedAt(null);

        $em->flush();

        $this->addFlash("success", "L'actualité a bien été restaurée");

        return $this->redirectToRoute('admin_actu');
    }
}
