<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\CategoryPhoto;
use App\Entity\Photo;
use App\Form\PhotoType;
use App\Form\PhotoEditType;
use App\Repository\PhotoRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class AdminPhotoController extends BaseController
{
    #[Route(path: '/{id}/photos', name: 'admin_cat_photos')]
    public function index(CategoryPhoto $cat, PhotoRepository $prepo)
    {
        $photos = $prepo->getPhotoCatByPos($cat->getId());
        $photosDeleted = $prepo->getPhotoCatByPosDeleted($cat->getId());

        return $this->render('admin/photos/catPhotos.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'cat' => $cat,
          'photos' => $photos,
          'photosDeleted' => $photosDeleted
        ]);
    }

    #[Route(path: '/{id}/addPhoto', name: 'admin_add_photo')]
    public function addPhoto(CategoryPhoto $cat, Request $request, EntityManagerInterface $em, FileUploaderService $fileUploaderService)
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imageFile = $form->get("path")->getData()) {
                $exif = @\exif_read_data($imageFile);

                $data = exif_read_data($imageFile, 'EXIF', true);
                
                if ($data === false) {
                    $exifs = [];
                } else {
                    $exifs = [
                        'model' => null,
                        'exposure' => null,
                        'aperture' => null,
                        'iso' => null,
                        'focal' => null
                    ];
    
                    if(is_string($data['IFD0']['Model']) || is_float($data['IFD0']['Model']))
                    {
                        $exifs['model'] = $data['IFD0']['Model'];
                    }
    
                    if(is_string($data['EXIF']['ExposureTime']) || is_float($data['EXIF']['ExposureTime']))
                    {
                        $exifs['exposure'] = $data['EXIF']['ExposureTime'];
                    }
    
                    if (is_string($data['EXIF']['FNumber']) || is_float($data['EXIF']['FNumber'])) {
                        $exifs['aperture'] = $data['EXIF']['FNumber'];
                    } else {
                        $exifs['aperture'] = $data['EXIF']['FNumber'][0] / $data['EXIF']['FNumber'][1];
                    }
    
                    if(is_string($data['EXIF']['ISOSpeedRatings']) || is_float($data['EXIF']['ISOSpeedRatings']))
                    {
                        $exifs['iso'] = $data['EXIF']['ISOSpeedRatings'];
                    }
    
                    if(is_string($data['EXIF']['FocalLength']) || is_float($data['EXIF']['FocalLength']))
                    {
                        $exifs['focal'] = $data['EXIF']['FocalLength'];
                    }
                }

                $photo->setExifs($exifs);

                $newFilename = $photo->getTitle();
                $directory = "/photo/" . $cat->getId() . "/photo";
                $imageFileName = $fileUploaderService->upload($imageFile, $newFilename, $directory);
                $photo->setPath($directory . "/" . $imageFileName);
                $photo->setCategoryPhoto($cat);
            }

            $em->persist($photo);
            $em->flush();
            $this->addFlash("success", "La photo a bien été ajoutée");
            return $this->redirectToRoute('admin_cat_photos', [
              'id' => $cat->getId()
            ]);
        }

        return $this->render('admin/photos/addPhoto.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{cat_id}/modifier-photo/{photo_id}', name: 'admin_edit_photo')]
    #[Entity('cat', expr: 'repository.find(cat_id)')]
    #[Entity('photo', expr: 'repository.find(photo_id)')]
    public function editPhoto(CategoryPhoto $cat, Photo $photo, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(PhotoEditType::class, $photo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash("success", "La photo a bien été modifiée");
            return $this->redirectToRoute('admin_cat_photos', [
              'id' => $cat->getId()
            ]);
        }

        return $this->render('admin/photos/addPhoto.html.twig', [
          'base' => $this->base,
          'expositonsCount' => $this->expositionsCount,
          'linksCount' => $this->linksCount,
          'actusCount' => $this->actusCount,
          'categoriesCount' => $this->categoriesCount,
          'photo' => $photo,
          'form' => $form->createView()
        ]);
    }

    #[Route(path: '/photo/sort', name: 'admin_photo_sort')]
    public function sortablePhoto(Request $request, EntityManagerInterface $em, PhotoRepository $prepo)
    {
        $photo_id = $request->request->get('photo_id');
        $position = $request->request->get('position');

        $photo = $prepo->findOneBy(['id' => $photo_id]);

        $photo->setPosition($position);

        try {
            $em->flush();
            return new Response(true);
        } catch (\PdoException $e) {
        }
    }

    #[Route(path: '/photo/delete/{id}/{hardDelete}', name: 'admin_delete_photo')]
    public function deletePhoto(Photo $photo, $hardDelete = 0, EntityManagerInterface $em, PhotoRepository $prepo, FileUploaderService $fileUploaderService)
    {
        $cat = $photo->getCategoryPhoto();

        if ($hardDelete) {
            try {
                if ($photo->getPath() !== null) {
                    $fileUploaderService->deleteFile($fileUploaderService->getTargetDirectory() . $photo->getPath());
                }

                $em->remove($photo);
                $em->flush();

                $this->addFlash("success", "La photo a bien été supprimé définitivement");
            } catch (Error $e) {
                return $this->addFlash("danger", "Une erreur est survenue, la photo n'a pas pu être supprimé");
            }
        } else {
            // Soft delete
            $prepo->remove($photo);

            $this->addFlash("success", "La photo a bien été supprimée");
        }

        return $this->redirectToRoute('admin_cat_photos', ['id' => $cat->getId()]);
    }

    #[Route(path: '/restore/{id}', name: 'admin_restore_photo')]
    public function restorePhoto(Photo $photo, EntityManagerInterface $em)
    {
        $cat = $photo->getCategoryPhoto();

        $photo->setDeletedAt(null);

        $em->flush();

        $this->addFlash("success", "La photo a bien été restaurée");

        return $this->redirectToRoute('admin_cat_photos', ['id' => $cat->getId()]);
    }
}
