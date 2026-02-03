<?php

namespace App\Presentation\Controller;

use App\Application\User\Command\RegisterUserCommand;
use App\Application\User\Command\RegisterUserHandler;
use App\Domain\User\Exception\InvalidEmailException;
use App\Domain\User\Exception\InvalidPasswordException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Auth Controller for the User Registration.
 *
 * Register a new user with these fields:
 * - Email: String of User Email
 * - Password: String of User Password
 * - Password Confirmation: String of User Password Confirmation
 *
 * Endpoints: POST /api/register
 */
class AuthController extends AbstractController
{
    public function __construct(
        private RegisterUserHandler $registerUserHandler,
    ) {
    }

    /**
     * Register a new user.
     *
     * Flow:
     * - Get User fields and create a new RegisterUserCommand
     * - Handle the request through RegisterUserHandler
     * - Return a JSON with status code and response of the registration process
     *
     * @param Request $httpRequest The HTTP POST Request
     *
     * @return Response JSON response with userId or error
     */
    #[Route('/api/register', 'api_register', methods: ['POST'])]
    public function register(Request $httpRequest): Response
    {
        $response = [];
        $statusCode = 201;

        try {
            $content = json_decode($httpRequest->getContent(), true);
            $command = new RegisterUserCommand(
                $content['email'] ?? '',
                $content['password'] ?? '',
                $content['password_confirmation'] ?? ''
            );

            $userId = $this->registerUserHandler->handle($command);
            if (empty($userId)) {
                throw new \Exception('Something goes wrong');
            }

            $response['userId'] = $userId->getValue();
        } catch (InvalidEmailException|InvalidPasswordException $e) {
            $statusCode = 400;
            $response['error'] = $e->getMessage();
        } catch (\DomainException $e) {
            $statusCode = 409;
            $response['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $statusCode = 500;
            $response['error'] = $e->getMessage();
        }

        return $this->json($response, $statusCode);
    }
}
