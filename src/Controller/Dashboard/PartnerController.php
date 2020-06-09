<?php

namespace App\Controller\Dashboard;

use App\Contracts\WithUpladableFile;
use App\Entity\Partner;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use App\Services\PhotosService;
use App\Traits\UploadFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/dashboard/partner")
 * @IsGranted("ROLE_PARTNERS")
 */
class PartnerController extends AbstractController implements WithUpladableFile
{

    use UploadFile;

    /**
     * @Route("/", name="partner_index", methods={"GET"})
     */
    public function index(PartnerRepository $partnerRepository): Response
    {
        return $this->render('dashboard/partner/index.html.twig', [
            'partners' => $partnerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="partner_new", methods={"GET","POST"})
     */
    public function new(Request $request, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $partner = new Partner();
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadOnePhoto($partner, 'image', $form, $photosService);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partner);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('Partner successfully created'));

            return $this->redirectToRoute('partner_index');
        }

        return $this->render('dashboard/partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partner_show", methods={"GET"})
     */
    public function show(Partner $partner): Response
    {
        return $this->render('dashboard/partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="partner_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Partner $partner, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadOnePhoto($partner, 'image', $form, $photosService);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('Partner successfully updated'));

            return $this->redirectToRoute('partner_index');
        }

        return $this->render('dashboard/partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partner_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Partner $partner): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partner->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($partner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('partner_index');
    }

    /**
     * @Route("/{id}/delete-image", name="partner_delete_image")
     */
    public function deleteImage(Partner $partner)
    {
        return $this->processDeleteOneAndUpdate($partner, 'image');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{id}/regenerate-photo", name="partner_regenerate_image")
     */
    public function renegerateImage(Partner $partner, PhotosService $photosService)
    {
        return $this->processRegenerateOneAndUpdate($partner, 'image', $photosService);
    }

    public function getEntityTypeIdentifier(): string
    {
        return Partner::class;
    }

}
