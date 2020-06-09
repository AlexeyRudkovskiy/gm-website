<?php

namespace App\Controller\Dashboard;

use App\Contracts\WithUpladableFile;
use App\Entity\Product;
use App\Form\ProductType;
use App\Services\PhotosService;
use App\Traits\UploadFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/dashboard/product")
 * @IsGranted("ROLE_PRODUCTS")
 */
class ProductController extends AbstractController implements WithUpladableFile
{

    use UploadFile;

    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('dashboard/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadOnePhoto($product, 'image', $form, $photosService);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('Product successfully created'));

            return $this->redirectToRoute('product_index');
        }

        return $this->render('dashboard/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('dashboard/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadOnePhoto($product, 'image', $form, $photosService);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('Product successfully updated'));

            return $this->redirectToRoute('product_edit', [ 'id' => $product->getId() ]);
        }

        return $this->render('dashboard/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/{id}/delete-image", name="product_delete_image")
     */
    public function deleteImage(Product $product)
    {
        return $this->processDeleteOneAndUpdate($product, 'image');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{id}/regenerate-photo", name="product_regenerate_image")
     */
    public function renegerateImage(Product $product, PhotosService $photosService)
    {
        return $this->processRegenerateOneAndUpdate($product, 'image', $photosService);
    }

    public function getEntityTypeIdentifier(): string
    {
        return Product::class;
    }

}
