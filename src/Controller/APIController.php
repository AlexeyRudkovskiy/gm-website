<?php

namespace App\Controller;

use App\Entity\ContactRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APIController
 * @package App\Controller
 *
 * @Route("/api")
 */
class APIController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @Route("/contact", methods={"POST"})
     */
    public function createContactRequest(Request $request)
    {
        $data = $this->validateAndGet($request, [
            'fullName',
            'phoneNumber',
            'email',
            'question'
        ]);

        if (!is_array($data)) {
            return $data;
        }

        $fullName = $data['fullName'];
        $phoneNumber = $data['phoneNumber'];
        $email = $data['email'];
        $question = $data['question'];

        $contactRequest = new ContactRequest();
        $contactRequest->setStatus('new');
        $contactRequest->setFullName($fullName);
        $contactRequest->setPhoneNumber($phoneNumber);
        $contactRequest->setEmail($email);
        $contactRequest->setQuestion($question);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($contactRequest);
        $manager->flush();

        return $this->json([
            'status' => 'success'
        ]);
    }

    private function validateAndGet(Request $request, array $fields) {
        $errors = [];
        $data = [];

        foreach ($fields as $field) {
            $value = $request->get($field, null);
            if ($value === null) {
                $errors[$field] = 'Field is required';
                continue;
            }

            $data[$field] = $value;
        }

        if (!empty($errors)) {
            return $this->json([
                'errors' => $errors
            ], 400);
        }

        return $data;
    }

}
