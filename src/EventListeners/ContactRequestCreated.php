<?php


namespace App\EventListeners;


use App\Entity\ContactRequest;
use App\Services\DashboardService;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Twig\Environment;

class ContactRequestCreated
{

    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var Environment */
    protected $twig;

    /** @var DashboardService */
    protected $dashboard;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, DashboardService $dashboardService)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->dashboard = $dashboardService;
    }

    public function postPersist(ContactRequest $contactRequest, LifecycleEventArgs $args)
    {
        $email = $this->dashboard->getSetting('contactRequestsEmail');
        $message = (new \Swift_Message('New contact request'))
            ->setFrom($email)
            ->setTo($email)
            ->setBody($this->twig->render('emails/contact_request.html.twig', [
                'request' => $contactRequest
            ]), 'text/html')
        ;

        $this->mailer->send($message);
    }

}
