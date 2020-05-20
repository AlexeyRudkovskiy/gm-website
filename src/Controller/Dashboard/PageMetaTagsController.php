<?php

namespace App\Controller\Dashboard;

use App\Entity\MetaTag;
use App\Entity\PageMetaTags;
use App\Form\PageMetaTagsType;
use App\Repository\PageMetaTagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard/meta-tags")
 * @IsGranted("ROLE_SEO")
 */
class PageMetaTagsController extends AbstractController
{
    /**
     * @Route("/", name="page_meta_tags_index", methods={"GET"})
     */
    public function index(PageMetaTagsRepository $pageMetaTagsRepository): Response
    {
        return $this->render('dashboard/page_meta_tags/index.html.twig', [
            'page_meta_tags' => $pageMetaTagsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="page_meta_tags_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pageMetaTag = new PageMetaTags();
        $form = $this->createForm(PageMetaTagsType::class, $pageMetaTag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $formData = $form->getData();
            $metaTags = $formData->getMetaTags();

            /** @var MetaTag $metaTag */
            foreach ($metaTags as $metaTag) {
                $metaTag->setPage($pageMetaTag);
                $entityManager->persist($metaTag);
            }


            $entityManager->persist($pageMetaTag);
            $entityManager->flush();

            return $this->redirectToRoute('page_meta_tags_index');
        }

        return $this->render('dashboard/page_meta_tags/new.html.twig', [
            'page_meta_tag' => $pageMetaTag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="page_meta_tags_show", methods={"GET"})
     */
    public function show(PageMetaTags $pageMetaTag): Response
    {
        return $this->render('dashboard/page_meta_tags/show.html.twig', [
            'page_meta_tag' => $pageMetaTag,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="page_meta_tags_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PageMetaTags $pageMetaTag): Response
    {
        $form = $this->createForm(PageMetaTagsType::class, $pageMetaTag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $formData = $form->getData();
            $metaTags = $formData->getMetaTags();

            /** @var MetaTag $metaTag */
            foreach ($metaTags as $metaTag) {
                if (
                    strlen($metaTag->getName()) < 1 ||
                    strlen($metaTag->getContent()) < 1 &&
                    $metaTag->getPage() !== null
                ) {
                    $pageMetaTag->removeMetaTag($metaTag);
                } else if ($metaTag->getPage() === null) {
                    $entityManager->persist($metaTag);
                    $metaTag->setPage($pageMetaTag);
                } else {
                    $entityManager->persist($metaTag);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('page_meta_tags_index');
        }

        return $this->render('dashboard/page_meta_tags/edit.html.twig', [
            'page_meta_tag' => $pageMetaTag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="page_meta_tags_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PageMetaTags $pageMetaTag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pageMetaTag->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pageMetaTag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('page_meta_tags_index');
    }
}
