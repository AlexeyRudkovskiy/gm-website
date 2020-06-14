<?php


namespace App\Controller\Dashboard;


use App\Services\PhotosService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @param PhotosService $photosService
     * @param Request $request
     *
     * @Route("/generate-thumbnails", name="dashboard_images_generate_thumbnails")
     */
    public function generateThumbnails(PhotosService $photosService, Request $request)
    {
//        $width = $request->get('width');
//        $height = $request->get('height');
//        $scale = $request->get('scale');
//        $top = $request->get('top');
//        $left = $request->get('left');
        $original = $request->get('original');
        $originalFiles = json_decode($original['files'], true) ?? [];
        $update = $request->get('update');


        $photosService->regenerate($originalFiles, $update, $original);

        return $this->json([
            'status' => 'success'
        ]);
    }

}
