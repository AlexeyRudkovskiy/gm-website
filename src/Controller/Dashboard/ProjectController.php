<?php

namespace App\Controller\Dashboard;

use App\Contracts\WithUpladableFile;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Services\PhotosService;
use App\Traits\UploadFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/dashboard/project")
 * @IsGranted("ROLE_PROJECTS")
 */
class ProjectController extends AbstractController implements WithUpladableFile
{

    use UploadFile;

    /**
     * @Route("/", name="project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('dashboard/project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     */
    public function new(Request $request, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadOnePhoto($project, 'image', $form, $photosService);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('Project successfully created'));

            return $this->redirectToRoute('project_index');
        }

        return $this->render('dashboard/project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     */
    public function show(Project $project): Response
    {
        return $this->render('dashboard/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project, PhotosService $photosService, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadOnePhoto($project, 'image', $form, $photosService);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('Project successfully updated'));

            return $this->redirectToRoute('project_index');
        }

        return $this->render('dashboard/project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }

    /**
     * @Route("/{id}/delete-image", name="project_delete_image")
     */
    public function deleteImage(Project $project)
    {
        return $this->processDeleteOneAndUpdate($project, 'image');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{id}/regenerate-photo", name="project_regenerate_image")
     */
    public function renegerateImage(Project $project, PhotosService $photosService)
    {
        return $this->processRegenerateOneAndUpdate($project, 'image', $photosService);
    }

    public function getEntityTypeIdentifier(): string
    {
        return Project::class;
    }

}
