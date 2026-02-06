<?php

namespace App\Presentation\Controller;

use App\Application\Exception\ValidationException;
use App\Application\User\Command\UpdateProfileCommand;
use App\Application\User\Command\UpdateProfileHandler;
use App\Domain\User\Repository\ProfileRepositoryInterface;
use App\Domain\User\ValueObject\HealthCondition;
use App\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * User Controller for retrieve and update User Informations.
 *
 * - GET User and Profile data
 *
 * Endpoints: GET /api/users/me
 */
class UserController extends AbstractController
{
    public function __construct(
        private Security $security,
        private ProfileRepositoryInterface $profileRepository,
        private UpdateProfileHandler $updateProfileHandler,
    ) {
    }

    /**
     * Get Full User Informations.
     *
     * Flow:
     * - Verify Authentication through JWT
     * - Get UserId and Search His Profile
     * - Return a JSON with status code and response of the User Data
     *
     * @return Response JSON response with user data or authentication error
     */
    #[Route('/api/users/me', name: 'api_users_me', methods: ['GET'])]
    public function me(): Response
    {
        $response = [
            'data' => [],
        ];
        $statusCode = 404;

        $securityUser = $this->security->getUser();
        if (!$securityUser) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $user = null;
        if ($securityUser instanceof SecurityUser) {
            $user = $securityUser->getUser();  // Domain User
        }

        if ($user) {
            $statusCode = 200;
            // Get Profile
            $profile = $this->profileRepository->findByUserId($user->getId());
            $response = [
                'data' => [
                    'id' => $user->getId()->getValue(),
                    'email' => $user->getEmail()->getValue(),
                    'profile' => $profile ? [
                        'first_name' => $profile->getFirstName(),
                        'last_name' => $profile->getLastName(),
                        'birth_date' => $profile->getBirthDay()->format('Y-m-d') ?? '',
                        'gender' => $profile->getGender()->value ?? '',
                        'height_cm' => (int) $profile->getHeight(),
                        'weight_kg' => (float) $profile->getWeight(),
                        'activity_level' => $profile->getActivityLevel()->value ?? '',
                        'dietary_goal' => $profile->getDietaryGoal()->value ?? '',
                        'health_conditions' => array_map(fn ($condition) => $condition->value, $profile->getHealthConditions()),
                    ] : null,
                    'created_at' => $user->getCreatedAt()->format('c') ?? '',
                ],
            ];
        }

        return $this->json($response, $statusCode);
    }

    /**
     * Update or create profile user data.
     *
     * Flow:
     * - Verify Authentication through JWT
     * - Validate all fields for Profile data
     * - Create the command and handle the request
     * - Return a JSON with status code and response of the Profile Data
     *
     * @return Response JSON response with profile data or errors
     */
    #[Route('/api/users/me/profile', name: 'api_users_me_profile', methods: ['POST'])]
    public function updateProfile(Request $httpRequest): Response
    {
        $statusCode = 200;
        $response = [
            'data' => [
                'profile' => [],
            ],
            'meta' => [
                'message' => 'Profile updated successfully',
            ],
        ];

        $securityUser = $this->security->getUser();
        if (!$securityUser) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $user = null;
        if ($securityUser instanceof SecurityUser) {
            $user = $securityUser->getUser();  // Domain User
        }

        $requestData = json_decode($httpRequest->getContent(), true);

        try {
            $command = new UpdateProfileCommand(
                $user->getId()->getValue(),
                $requestData['first_name'] ?? '',
                $requestData['last_name'] ?? '',
                $requestData['birth_date'] ?? '',
                $requestData['gender'] ?? '',
                (int) ($requestData['height_cm'] ?? 0),
                (float) ($requestData['weight_kg'] ?? 0),
                $requestData['activity_level'] ?? '',
                $requestData['dietary_goal'] ?? '',
                $requestData['health_conditions'] ?? []
            );
            $profile = $this->updateProfileHandler->handle($command);
            $response['data']['profile'] = [
                'profile_id' => $profile->getProfileId()->getValue(),
                'first_name' => $profile->getFirstName(),
                'last_name' => $profile->getLastName(),
                'birth_date' => $profile->getBirthDay()->format('Y-m-d') ?? '',
                'gender' => $profile->getGender()->value ?? '',
                'height_cm' => (int) $profile->getHeight(),
                'weight_kg' => (float) $profile->getWeight(),
                'activity_level' => $profile->getActivityLevel()->value ?? '',
                'dietary_goal' => $profile->getDietaryGoal()->value ?? '',
                'health_conditions' => array_map(
                    fn (HealthCondition $c) => $c->value,
                    $profile->getHealthConditions()
                ),
            ];
        } catch (ValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => $e->getMessage(),
                    'details' => $e->getErrors(),
                ],
            ], 400);
        }

        return $this->json($response, $statusCode);
    }
}
