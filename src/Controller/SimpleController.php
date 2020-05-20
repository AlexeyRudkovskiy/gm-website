<?php


namespace App\Controller;


use App\Entity\ContactRequest;
use App\Repository\ContactRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SimpleController
 * @package App\Controller
 *
 * @Route("/simple")
 */
class SimpleController extends AbstractController
{

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/")
     */
    public function index(\Swift_Mailer $mailer, ContactRequestRepository $repository)
    {
        $record = $repository->findOneBy([], [ 'id' => 'desc' ]);

        $params = [
            'request' => $record
        ];

        $message = (new \Swift_Message('New contact request'))
            ->setFrom('andreyh322@gmail.com')
            ->setTo('alexteen12@gmail.com')
            ->setBody($this->renderView('emails/contact_request.html.twig', $params), 'text/html')
        ;

        $mailer->send($message);
        return $this->json([ 'stay' => 'home', 'test' => $record ]);
    }

}
