<?php

namespace App\Tests\Integration\Presentation\Controller;

use App\Domain\User\Entity\Profile;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\ProfileRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\ProfileId;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Integration tests for User Data Controller.
 *
 * Tests the the read of data from an authenticated user:
 * - Get User Data with valid credentials
 * - Access to protected routes with valid/invalid tokens
 * Test the creation or update of a profile for an authenticated user:
 * - Create a new Profile with a valid data
 * - Create a new Profile with missing mandatory fields
 * - Create a new Profile with invalid Gender
 * - Update a profile with valid data
 * - Create a profile with invalid tokens
 *
 * Endpoints tested: GET /api/users/me, POST /api/users/me/profile
 */
class UserTestController extends WebTestCase
{
    private KernelBrowser $httpClient;
    private UserRepositoryInterface $userRepository;
    private ProfileRepositoryInterface $profileRepository;
    private PasswordHasherInterface $passwordHasher;
    private User $testUser;
    private Profile $testProfile;
    private string $testEmail = 'registrationtest@example.com';
    private string $testPassword = 'password123';
    private string $userLoginEndpoint = '/api/login';
    private string $usersMeEndpoint = '/api/users/me';
    private string $usersMeProfileEndpoint = '/api/users/me/profile';

    /**
     * Set up test environment before each test.
     *
     * Initializes:
     * - HttpClient: to make HTTP requests to endpoints
     * - UserRepository: to persist and retrieve test users
     * - PasswordHasher: to hash passwords for test users
     *
     * Creates a test user that will be used across all test methods.
     */
    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
        self::bootKernel();

        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepositoryInterface::class);
        $this->profileRepository = $container->get(ProfileRepositoryInterface::class);
        $this->passwordHasher = $container->get(PasswordHasherInterface::class);

        // Register Test user for all methods
        $this->testUser = $this->createTestUser($this->testEmail, $this->testPassword);

        parent::setUp();
    }

    /**
     * Create and persist a test user in the database.
     *
     * @param string $email    The email address for the test user
     * @param string $password The plain text password (will be hashed)
     *
     * @return User|null The created user, or null if creation failed
     */
    private function createTestUser(string $email = 'test@example.com', string $password = 'hashed_password_123'): ?User
    {
        $user = User::register(
            new Email($email),
            $this->passwordHasher->hash($password)
        );

        $this->userRepository->save($user);

        $found = $this->userRepository->findById($user->getId());
        $this->assertNotNull($found, 'User Correctly Created');

        if (!is_null($found)) {
            return $found;
        }

        return null;
    }

    /**
     * Login and return JWT token.
     *
     * @return string The JWT token
     */
    private function loginAndGetToken(): string
    {
        $this->httpClient->request(
            'POST',
            $this->userLoginEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => $this->testEmail, 'password' => $this->testPassword])
        );

        $response = $this->httpClient->getResponse();
        $data = json_decode($response->getContent(), true);

        return $data['token'] ?? '';
    }

    /**
     * Test access to the protected route /api/users/me with Correct JWT and get the right user data.
     *
     * Expects:
     * - HTTP 200 status code
     * - Response contain the user data expected without profile data
     */
    public function testGetUserMeWithValidTokenWithoutProfile(): void
    {
        $token = $this->loginAndGetToken();
        $this->httpClient->request(
            'GET',
            $this->usersMeEndpoint,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $responseUserMe = $this->httpClient->getResponse();
        $data = json_decode($responseUserMe->getContent(), true);

        $this->assertEquals('200', $responseUserMe->getStatusCode(), 'Endpoint /users/me should return 200 code on valid token');
        $this->assertArrayHasKey('data', $data, 'Response should contain data key');
        $this->assertArrayHasKey('id', $data['data'], 'Response data should contain id');
        $this->assertArrayHasKey('email', $data['data'], 'Response data should contain email');
        $this->assertArrayHasKey('profile', $data['data'], 'Response data should contain profile');
        $this->assertArrayHasKey('created_at', $data['data'], 'Response data should contain created_at');
        $this->assertEquals($this->testEmail, $data['data']['email'], 'Email should match authenticated user');
        $this->assertNull($data['data']['profile'], 'Profile should be null for user without profile');
    }

    /**
     * Test access to the protected route /api/users/me with invalid JWT Token.
     *
     * Expects:
     * - HTTP 401 status code
     */
    public function testGetUserMeWithoutValidToken(): void
    {
        $token = 'wrong_token';

        $this->httpClient->request(
            'GET',
            $this->usersMeEndpoint,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $responseUserMe = $this->httpClient->getResponse();
        $this->assertEquals('401', $responseUserMe->getStatusCode(), 'Endpoint /users/me should return 401 code on invalid token');
    }

    /**
     * Test create a new profile for a specific user with all valid data to the protected route /api/users/me/profile with valid JWT Token.
     *
     * Expects:
     * - HTTP 200 status code
     * - Assert equals between data sent and the response
     */
    public function testCreateProfileWithValidData(): void
    {
        $token = $this->loginAndGetToken();

        // Act
        $profile_data = [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'birth_date' => '1985-03-15',
            'gender' => 'male',
            'height_cm' => 175,
            'weight_kg' => 78.5,
            'activity_level' => 'moderate',
            'dietary_goal' => 'weight_loss',
            'health_conditions' => ['celiac', 'lactose_intolerance'],
        ];

        $this->httpClient->request(
            'POST',
            $this->usersMeProfileEndpoint,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
            json_encode($profile_data)
        );

        $responseProfile = $this->httpClient->getResponse();
        $responseData = json_decode($responseProfile->getContent(), true);

        $profile_id = $responseData['data']['profile']['profile_id'] ?? '';
        if ($profile_id) {
            $this->testProfile = $this->profileRepository->findById(new ProfileId($profile_id));
        }

        // Assert
        $this->assertEquals('200', $responseProfile->getStatusCode(), 'Endpoint /users/me/profile should return 200 code on valid token');
        $this->assertNotEmpty($profile_id, 'Profile Id Should be not empty');
        $this->assertEquals($profile_data['first_name'], $responseData['data']['profile']['first_name'], 'First Name should match created Profile');
        $this->assertEquals($profile_data['last_name'], $responseData['data']['profile']['last_name'], 'Last Name should match created Profile');
        $this->assertEquals($profile_data['birth_date'], $responseData['data']['profile']['birth_date'], 'Birth Date should match created Profile');
        $this->assertEquals($profile_data['gender'], $responseData['data']['profile']['gender'], 'Gender should match created Profile');
        $this->assertEquals($profile_data['height_cm'], $responseData['data']['profile']['height_cm'], 'Height Cm should match created Profile');
        $this->assertEquals($profile_data['weight_kg'], $responseData['data']['profile']['weight_kg'], 'Weight Kg should match created Profile');
        $this->assertEquals($profile_data['activity_level'], $responseData['data']['profile']['activity_level'], 'Activity Level should match created Profile');
        $this->assertEquals($profile_data['dietary_goal'], $responseData['data']['profile']['dietary_goal'], 'Dietary Goal should match created Profile');
        $this->assertEquals($profile_data['health_conditions'], $responseData['data']['profile']['health_conditions'], 'Health Conditions should match created Profile');
    }

    /**
     * Test create a new profile for a specific user with invalid data.
     *
     * Expects:
     * - HTTP 400 status code
     * - Error Code Value
     * - Fields Missing Key
     */
    public function testCreateProfileWithMissingMandatoryFields(): void
    {
        // Arrange
        if (isset($this->testProfile)) {
            $this->profileRepository->delete($this->testProfile);
        }

        $token = $this->loginAndGetToken();

        // Act
        $profile_data = [
            'first_name' => 'Mario',
            'last_name' => '',
            'birth_date' => '1985-03-15',
            'gender' => '',
            'height_cm' => 175,
            'weight_kg' => 78.5,
            'activity_level' => 'moderate',
            'dietary_goal' => 'weight_loss',
        ];

        $this->httpClient->request(
            'POST',
            $this->usersMeProfileEndpoint,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
            json_encode($profile_data)
        );

        $responseProfile = $this->httpClient->getResponse();
        $responseData = json_decode($responseProfile->getContent(), true);

        // Assert
        $this->assertEquals('400', $responseProfile->getStatusCode(), 'Endpoint /users/me/profile should return 400 code on missing fields');
        $this->assertEquals('VALIDATION_ERROR', $responseData['error']['code'], 'Response code should be "VALIDATION ERROR"');
        $this->assertNotEmpty($responseData['error']['details'], 'Response details should not be empty');
        if (!empty($responseData['error']['details'])) {
            foreach ($responseData['error']['details'] as $error) {
                $this->assertContains($error['field'], ['last_name', 'gender'], 'Error field should be last_name or gender');
            }
        }
    }

    /**
     * Test create a new profile for a specific user with invalid data.
     *
     * Expects:
     * - HTTP 400 status code
     * - Error Code Value
     * - Invalid Fields Key
     */
    public function testCreateProfileWithInvalidFields(): void
    {
        // Arrange
        if (isset($this->testProfile)) {
            $this->profileRepository->delete($this->testProfile);
        }

        $token = $this->loginAndGetToken();

        // Act
        $profile_data = [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'birth_date' => '1985-03-15',
            'gender' => 'wrong',
            'height_cm' => 175,
            'weight_kg' => 78.5,
            'activity_level' => 'low_moderate',
            'dietary_goal' => 'weight_loss',
        ];

        $this->httpClient->request(
            'POST',
            $this->usersMeProfileEndpoint,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
            json_encode($profile_data)
        );

        $responseProfile = $this->httpClient->getResponse();
        $responseData = json_decode($responseProfile->getContent(), true);

        // Assert
        $this->assertEquals('400', $responseProfile->getStatusCode(), 'Endpoint /users/me/profile should return 400 code on missing fields');
        $this->assertEquals('VALIDATION_ERROR', $responseData['error']['code'], 'Response code should be "VALIDATION ERROR"');
        $this->assertNotEmpty($responseData['error']['details'], 'Response details should not be empty');
        if (!empty($responseData['error']['details'])) {
            foreach ($responseData['error']['details'] as $error) {
                $this->assertContains($error['field'], ['activity_level', 'gender'], 'Error field should be activity_level or gender');
            }
        }
    }

    /**
     * Test update a profile for a specific user with all valid data to the protected route /api/users/me/profile with valid JWT Token.
     *
     * Expects:
     * - HTTP 200 status code
     * - Assert equals between data sent and the response
     */
    public function testUpdateProfileWithValidData(): void
    {
        $token = $this->loginAndGetToken();

        // Act
        $profile_data = [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'birth_date' => '1985-03-15',
            'gender' => 'male',
            'height_cm' => 175,
            'weight_kg' => 78.5,
            'activity_level' => 'moderate',
            'dietary_goal' => 'weight_loss',
            'health_conditions' => ['celiac', 'lactose_intolerance'],
        ];

        $this->httpClient->request(
            'POST',
            $this->usersMeProfileEndpoint,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
            json_encode($profile_data)
        );

        $responseProfile = $this->httpClient->getResponse();
        $responseData = json_decode($responseProfile->getContent(), true);

        $profile_update_data = [
            'weight_kg' => 73.5,
            'health_conditions' => ['lactose_intolerance'],
        ];

        $this->httpClient->request(
            'POST',
            $this->usersMeProfileEndpoint,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            ],
            json_encode($profile_update_data)
        );

        $responseUpdateProfile = $this->httpClient->getResponse();
        $responseUpdateData = json_decode($responseUpdateProfile->getContent(), true);

        $profile_id = $responseUpdateData['data']['profile']['profile_id'] ?? '';
        if ($profile_id) {
            $this->testProfile = $this->profileRepository->findById(new ProfileId($profile_id));
        }

        // Assert
        $this->assertEquals('200', $responseUpdateProfile->getStatusCode(), 'Endpoint /users/me/profile should return 200 code on valid token');
        $this->assertNotEmpty($profile_id, 'Profile Id Should be not empty');
        $this->assertEquals($profile_data['first_name'], $responseData['data']['profile']['first_name'], 'First Name should match created Profile');
        $this->assertEquals($profile_data['last_name'], $responseData['data']['profile']['last_name'], 'Last Name should match created Profile');
        $this->assertEquals($profile_data['birth_date'], $responseData['data']['profile']['birth_date'], 'Birth Date should match created Profile');
        $this->assertEquals($profile_data['gender'], $responseData['data']['profile']['gender'], 'Gender should match created Profile');
        $this->assertEquals($profile_data['height_cm'], $responseData['data']['profile']['height_cm'], 'Height Cm should match created Profile');
        $this->assertEquals($profile_update_data['weight_kg'], $responseUpdateData['data']['profile']['weight_kg'], 'Weight Kg should match updated Profile');
        $this->assertEquals($profile_data['activity_level'], $responseData['data']['profile']['activity_level'], 'Activity Level should match created Profile');
        $this->assertEquals($profile_data['dietary_goal'], $responseData['data']['profile']['dietary_goal'], 'Dietary Goal should match created Profile');
        foreach ($responseUpdateData['data']['profile']['health_conditions'] as $condition) {
            $this->assertContains($condition, $profile_update_data['health_conditions'], 'Health Conditions should match updated Profile');
        }
    }

    /**
     * Test access to the protected route /api/users/me/profile with invalid JWT Token.
     *
     * Expects:
     * - HTTP 401 status code
     */
    public function testCreateProfileWithoutToken(): void
    {
        $token = 'wrong_token';

        $this->httpClient->request(
            'POST',
            $this->usersMeProfileEndpoint,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $responseUserMe = $this->httpClient->getResponse();
        $this->assertEquals('401', $responseUserMe->getStatusCode(), 'Endpoint /users/me/profile should return 401 code on invalid token');
    }

    /**
     * Tear Down test environment after all tests.
     *
     * Remove test user from the Database.
     */
    protected function tearDown(): void
    {
        if (isset($this->testProfile)) {
            $this->profileRepository->delete($this->testProfile);
        }

        // Remove test user created in setUp
        if (isset($this->testUser)) {
            $this->userRepository->delete($this->testUser);
        }

        parent::tearDown();
    }
}
