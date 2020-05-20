<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\StaticPage;
use App\Repository\ProductRepository;
use App\Repository\StaticPageRepository;
use App\Services\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProdController
 * @package App\Controller
 */
class ProdController extends AbstractController
{

    public function index(DashboardService $dashboardService, ProductRepository $productRepository)
    {
//        $doctrine = $this->getDoctrine();
//        $manager = $doctrine->getManager();
////
//        $product = new Product();
////        /** @var ProductTranslation $translation
////         */
//        $translation = $product->translate('en');
////
//        $translation->setTitle('Test');
//        $translation->setDescription('Test');
//        $translation->setContent('Test');
//
//        $translation = $product->translate('ru');
//        $translation->setTitle('Тест');
//        $translation->setDescription('Тест');
//        $translation->setContent('Тест');
//
////
//        $product->mergeNewTranslations();
//        $manager->persist($product);
//        $manager->flush();

//        foreach ($product->getTranslations() as $translation) {
//            $manager->persist($translation);;
//        }
//
//        $manager->flush();

        $p = $productRepository->findOneBy([], ['id' => 'desc']);

        return $this->json([
            'title' => $p->getTitle(),
            'content' => $p->getContent(),
            'description' => $p->getDescription()
        ]);

        return $this->render('prod/index.html.twig', [  ]);
    }

    /**
     * @param string $slug
     * @return Response
     *
     * @Route("/page/{slug}", methods={"GET"})
     */
    public function staticPage(string $slug, StaticPageRepository $repository)
    {
        /** @var StaticPage $page */
        $page = $repository->findBySlug($slug);

        return $this->render('prod/staticPage.html.twig', [
            'page' => $page
        ]);
    }

}
