<?php


namespace App\Controller\Dashboard;


use App\Services\PhotosService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImagesController
 * @package App\Controller\Dashboard
 *
 * @Route("dashboard/images")
 */
class ImagesController extends AbstractController
{

    /**
     * @param PhotosService $photosService
     *
     * @Route("/sizes", name="dashboard_images_sizes")
     */
    public function getSizes(PhotosService $photosService)
    {
        return $this->json($photosService->getSizes());
    }

}
