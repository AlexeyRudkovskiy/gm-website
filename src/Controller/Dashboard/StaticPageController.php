<?php

namespace App\Controller\Dashboard;

use App\Contracts\WithUpladableFile;
use App\Entity\StaticPage;
use App\Form\StaticPageType;
use App\Repository\StaticPageRepository;
use App\Services\PhotosService;
use App\Traits\Slugify;
use App\Traits\UploadFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/dashboard/static-page")
 * @IsGranted("ROLE_SEO")
 */
class StaticPageController extends AbstractController implements WithUpladableFile
{

    use UploadFile;
    use Slugify;

    /**
     * @Route("/", name="static_page_index", methods={"GET"})
     */
    public function index(StaticPageRepository $staticPageRepository): Response
    {
        return $this->render('dashboard/static_page/index.html.twig', [
            'static_pages' => $staticPageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="static_page_new", methods={"GET","POST"})
     */
    public function new(Request $request, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $staticPage = new StaticPage();
        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photos = $this->uploadMultiplePhotos($staticPage, 'photos', $form, $photosService, true);
            $staticPage->setPhotos($photos);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staticPage);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('Static page successfully created'));

            return $this->redirectToRoute('static_page_index');
        }

        return $this->render('dashboard/static_page/new.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="static_page_show", methods={"GET"})
     */
    public function show(StaticPage $staticPage): Response
    {
        return $this->render('dashboard/static_page/show.html.twig', [
            'static_page' => $staticPage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="static_page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, StaticPage $staticPage, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photos = $this->uploadMultiplePhotos($staticPage, 'photos', $form, $photosService, true);
            $currentPhotos = $staticPage->getPhotos() ?? [];
            foreach ($photos as $photo) {
                array_push($currentPhotos, $photo);
            }
            $staticPage->setPhotos($currentPhotos);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('Static page successfully updated'));

            return $this->redirectToRoute('static_page_edit', [ 'id' => $staticPage->getId() ]);
        }

        return $this->render('dashboard/static_page/edit.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="static_page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, StaticPage $staticPage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$staticPage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($staticPage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('static_page_index');
    }



    /**
     * @param Request $request
     * @param StaticPage $staticPage
     * @return Response
     *
     * @Route("/{id}/update-photos-order", name="static_page_update_photos_order", methods={"POST"})
     */
    public function updatePhotosFor(Request $request, StaticPage $staticPage): Response
    {
        $currentPhotos = $staticPage->getPhotos();
        $newPhotos = [];
        $newOrder = $request->get('orders');

        foreach ($newOrder as $current => $new) {
            $newPhotos[$new] = $currentPhotos[$current];
        }

        $staticPage->setPhotos($newPhotos);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($staticPage);
        $manager->flush();

        return $this->json([
            'status' => 'success'
        ]);
    }

    /**
     * @param Request $request
     * @param StaticPage $staticPage
     * @return Response
     *
     * @Route("/{id}/delete-photo", name="static_page_delete_photo", methods={"DELETE"})
     */
    public function deletePhoto(Request $request, StaticPage $staticPage): Response
    {
        $photos = $staticPage->getPhotos();
        $index = (int)($request->get('index', -1));
        if ($index > -1) {
            $photo = $photos[$index];
            array_splice($photos, $index, 1);

            $this->deletePhotoFromDisk($photo);
            $staticPage->setPhotos($photos);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($staticPage);
            $manager->flush();

            return $this->json([
                'success' => true,
                'photos' => $photos,
                'photo' => $photo
            ]);
        } else {
            return $this->json([
                'success' => false
            ], 500);
        }
    }



    /**
     * @Route("/{id}/generate-photo", methods={"GET"}, name="static_page_generate_photo")
     */
    public function generatePhoto(Request $request, StaticPage $staticPage, PhotosService $photosService)
    {
        $photos = $staticPage->getPhotos();
        $photos = $this->regeneratePreviews($photos, $request, $photosService);

        $staticPage->setPhotos($photos);

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();

        $manager->persist($staticPage);
        $manager->flush();

        return $this->json([
            'status' => 'success'
        ]);
    }

    public function getEntityTypeIdentifier(): string
    {
        return StaticPage::class;
    }

}
