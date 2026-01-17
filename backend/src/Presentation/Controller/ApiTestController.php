<?php

namespace App\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiTestController extends AbstractController
{
    public function __construct(
        private Security $security,
    ) {
    }

    #[Route('/api/me', 'api_me', methods: ['GET'])]
    public function main(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $response = [
            'email' => $this->security->getUser()->getUserIdentifier(),
            'roles' => $this->security->getUser()->getRoles(),
        ];

        return $this->json($response);
    }
}
