<?php

namespace App\Controller\Admin;

use App\Form\CatType;
use App\Repository\PhotoRepository;
use App\Service\FileUploaderService;
use App\Controller\BaseController;
use App\Entity\CategoryPhoto;
use App\Repository\CategoryPhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/cat')]
#[IsGranted('ROLE_ADMIN')]
class AdminCatController extends BaseController
{
    #[Route(path: '/', name: 'admin_categories')]
    public function Index(CategoryPhotoRepository $pcrepo, PhotoRepository $prepo)
    {
        // $cats = $pcrepo->findAll();
        $cats = $pcrepo->findAllOrderByPos();
        $catsDeleted = $pcrepo->findAllOrderByPosDeleted();

        return $this->render('admin/cats/index.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'cats' => $cats,
          'catsDeleted' => $catsDeleted
        ]);
    }

    #[Route(path: '/editCat/{id}', name: 'admin_edit_cat')]
    #[Route(path: '/addCat', name: 'admin_add_cat')]
    public function add_cat(CategoryPhoto $cat = null, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
    {
        if (!$cat) {
            $cat = new CategoryPhoto();
        }

        $form = $this->createForm(CatType::class, $cat);

        $form->handleRequest($request);

        if ($imageFile = $form->get("path")->getData()) {
            $newFilename = $cat->getTitle();
        }

        if ($form->isSubmitted() && $form->isValid()) {            
            $isRandomImage = $form->get("is_random_image")->getData();
            $isImage = $form->get("path")->getData();

            if (!$isRandomImage && !$isImage) {
                $this->addFlash("danger", "Vous devez choisir une image ou une image al??atoire");
                return $this->redirectToRoute('admin');
            }

            if ($isRandomImage) {
                $cat->setIsRandomImage(true);
                $cat->setPhotoCoverPath(null);

                if ($this->base->getHomepageImagePath() !== null) {
                    $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $cat->getPhotoCoverPath());
                }
            } else {
                if ($imageFile === $isImage) {
                    $directory = "/photo/cover";
                    $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
                    $cat->setPhotoCoverPath($directory . "/" . $imageFileName);
                }
            }

            $em->persist($cat);
            $em->flush();

            $this->addFlash("success", "La cat??gorie a bien ??t?? cr??e/modifi??");
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/cats/addCat.html.twig', [
            'form' => $form->createView(),
            'base' => $this->base,
            'expositonsCount' => $this->expositionsCount,
            'linksCount' => $this->linksCount,
            'actusCount' => $this->actusCount,
            'categoriesCount' => $this->categoriesCount,
            'cat' => $cat
        ]);
    }

    #[Route(path: '/delete/{id}/{hardDelete}', name: 'admin_delete_cat')]
    public function deleteCat(CategoryPhoto $cat, $hardDelete = 0, EntityManagerInterface $em, PhotoRepository $prepo, CategoryPhotoRepository $cprepo, FileUploaderService $fileUploaderService)
    {
        if ($hardDelete) {
            try {
                $photos = $prepo->getPhotoCatByPos($cat->getId());

                $directory = "photo/" . $cat->getId();

                $fileUploaderService->deleteTree($fileUploaderService->getTargetDirectory() . $directory);
                $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $cat->getPhotoCoverPath());

                foreach ($photos as $photo) {
                    $em->remove($photo);
                }

                $em->remove($cat);
                $em->flush();

                $this->addFlash("success", "La cat??gorie, et les photos associ??s, ont bien ??t?? supprim??s d??finitivement");
            } catch (Error $e) {
                return $this->addFlash("danger", "Une erreur est survenue, la cat??gorie n'a pas pu ??tre supprim??");
            }
        } else {
            // Soft delete
            $cprepo->remove($cat);
            $em->flush();

            $this->addFlash("success", "La cat??gorie a bien ??t?? supprim??");
        }

        return $this->redirectToRoute('admin_categories');
    }

    #[Route(path: '/sort', name: 'admin_cat_sort')]
    public function sortableCat(Request $request, EntityManagerInterface $em, CategoryPhotoRepository $lrepo)
    {
        $cat_id = $request->request->get('cat_id');
        $position = $request->request->get('position');

        $cat = $lrepo->findOneBy(['id' => $cat_id]);

        $cat->setPosition($position);

        try {
            $em->flush();
            return new Response(true);
        } catch (\PdoException $e) {
        }
    }

    #[Route(path: '/restore/{id}', name: 'admin_restore_cat')]
    public function restoreCat(CategoryPhoto $cat, EntityManagerInterface $em)
    {
        $cat->setDeletedAt(null);
        $em->flush();

        $this->addFlash("success", "La cat??gorie a bien ??t?? restaur??");

        return $this->redirectToRoute('admin_categories');
    }
}
