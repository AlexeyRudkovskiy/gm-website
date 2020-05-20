<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    /**
     * @Route("/session", name="session")
     */
    public function index(Session $session)
    {
        $counter = $session->get('counter', 1);
        $counter++;
        $session->set('counter', $counter);

        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
            'counter' => $counter
        ]);
    }
}
