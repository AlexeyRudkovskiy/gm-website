<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\StaticPage;
use App\Repository\PartnerRepository;
use App\Repository\ProductRepository;
use App\Repository\ProjectRepository;
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

    public function index(
        DashboardService $dashboardService,
        ProductRepository $productRepository,
        ProjectRepository $projectRepository,
        PartnerRepository $partnerRepository,
        StaticPageRepository $staticPageRepository
    )
    {
        $products = $productRepository->findAll();
        $projects = $projectRepository->findAll();
        $partners = $partnerRepository->findAll();

        $firstBlock = $dashboardService->getSetting('firstBlock');
        $secondBlock = $dashboardService->getSetting('secondBlock');

        $firstBlock = $staticPageRepository->find($firstBlock);
        $secondBlock = $staticPageRepository->find($secondBlock);

        $slider = $staticPageRepository->findBySlug('slider');

        return $this->render('prod/index.html.twig', [
            'products' => $products,
            'projects' => $projects,
            'partners' => $partners,

            'first_block' => $firstBlock,
            'second_block' => $secondBlock,
            'slider' => $slider
        ]);
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
