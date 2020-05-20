<?php

namespace App\Controller\Dashboard;

use App\Entity\ContactRequest;
use App\Repository\ContactRequestRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactRequestController
 * @package App\Controller
 *
 * @Route("/dashboard/contact-request")
 * @IsGranted("ROLE_MANAGER")
 */
class ContactRequestController extends AbstractController
{
    /**
     * @Route("/", name="contact_request_index")
     * @param ContactRequestRepository $contactRequestRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(ContactRequestRepository $contactRequestRepository, PaginatorInterface $paginator, Request $request)
    {
        $requests = $contactRequestRepository->paginate($paginator, $request);

        return $this->render('dashboard/contact_request/index.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @param ContactRequest $contactRequest
     * @return Response
     *
     * @Route("/{id}", name="contact_request_show", methods={"GET"})
     */
    public function show(ContactRequest $contactRequest)
    {
        return $this->render('dashboard/contact_request/show.html.twig', [
            'request' => $contactRequest
        ]);
    }

    /**
     * @param ContactRequest $contactRequest
     * @return Response
     *
     * @Route("/{id}/mark-as-done", name="contact_request_mark_as_done", methods={"GET"})
     */
    public function markAsDone(ContactRequest $contactRequest)
    {
        $contactRequest->setStatus('solved');
        $this->getDoctrine()->getManager()->persist($contactRequest);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('contact_request_show', ['id' => $contactRequest->getId()]);
    }

}
