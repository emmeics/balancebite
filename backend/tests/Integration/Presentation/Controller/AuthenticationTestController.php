<?php

namespace App\Tests\Integration\Presentation\Controller;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\Email;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Integration tests for JWT Authentication and Registration flow.
 *
 * Tests the complete authentication cycle:
 * - User login with valid/invalid credentials
 * - Access to protected routes with valid/invalid tokens
 * Tests registration process:
 * - User with all valid fields
 * - User with invalid email
 * - User with invalid password
 * - User with mismatch password
 * - User with an existant email
 *
 * Endpoints tested: POST /api/login, GET /api/me, POST /api/register
 */
class AuthenticationTestController extends WebTestCase
{
    private KernelBrowser $httpClient;
    private UserRepositoryInterface $userRepository;
    private PasswordHasherInterface $passwordHasher;
    private string $testEmail = 'logintest@example.com';
    private string $testRegistrationEmail = 'testnewuser2@email.com';
    private string $testPassword = 'password123';
    private string $apiLoginEndpoint = '/api/login';
    private string $apiRegisterEndpoint = '/api/register';
    private User $testUser;

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
        $this->passwordHasher = $container->get(PasswordHasherInterface::class);

        // Register Test user for all methods
        $this->testUser = $this->createTestUser($this->testEmail, $this->testPassword);
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
     * Test successful login with valid credentials.
     *
     * Expects:
     * - HTTP 200 status code
     * - Response contains 'token' key with JWT
     */
    public function testLoginWithCorrectCredentials(): void
    {
        $this->httpClient->request(
            'POST',
            $this->apiLoginEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => $this->testEmail, 'password' => $this->testPassword])
        );

        $response = $this->httpClient->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode(), 'Login should return HTTP 200 on valid credentials');
        $this->assertArrayHasKey('token', $content, 'Correct User Authentication After Registration');
    }

    /**
     * Test bad login with invalid credentials.
     *
     * Expects:
     * - HTTP 401 status code
     */
    public function testLoginWithWrongCredentials(): void
    {
        $this->httpClient->request(
            'POST',
            $this->apiLoginEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => $this->testEmail, 'password' => 'wrong_password'])
        );

        $response = $this->httpClient->getResponse();

        $this->assertEquals(401, $response->getStatusCode(), 'Login should return HTTP 401 on invalid credentials');
    }

    /**
     * Test access to the protected route /api/me with Correct JWT.
     *
     * Expects:
     * - HTTP 200 status code
     * - Response contain the same email used in the login request
     */
    public function testProtectedRouteWithCorrectAuthToken(): void
    {
        // Arrange: Login to get a valid token
        $this->httpClient->request(
            'POST',
            $this->apiLoginEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => $this->testEmail, 'password' => $this->testPassword])
        );

        $response = $this->httpClient->getResponse();
        $content = json_decode($response->getContent(), true);

        // Act: Access protected route with valid token
        $this->httpClient->request(
            'GET',
            '/api/me',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$content['token']]
        );

        $responseApiMe = $this->httpClient->getResponse();
        $contentApiMe = json_decode($responseApiMe->getContent(), true);

        // Assert: Verify successful access
        $this->assertEquals(200, $responseApiMe->getStatusCode(), 'Response should return HTTP 200 on valid JWT');
        $this->assertEquals($this->testEmail, $contentApiMe['email'], 'Response should return the same email that identify the user with the valid JWT');
    }

    /**
     * Test access to the protected route /api/me with Invalid JWT.
     *
     * Expects:
     * - HTTP 401 status code
     */
    public function testProtectedRouteWithWrongAuthToken(): void
    {
        $token = 'wrong_token';

        $this->httpClient->request(
            'GET',
            '/api/me',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $response = $this->httpClient->getResponse();
        $this->assertEquals(401, $response->getStatusCode(), 'Request should return HTTP 401 on invalid JWT');
    }

    /**
     * Test successful register with valid fields.
     *
     * Expects:
     * - HTTP 201 status code
     */
    public function testRegisterUserWithValidData(): void
    {
        $this->httpClient->request(
            'POST',
            $this->apiRegisterEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $this->testRegistrationEmail, 'password' => $this->testPassword, 'password_confirmation' => $this->testPassword])
        );

        $response = $this->httpClient->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode(), 'Register should return HTTP 201 on valid fields');
        $this->assertArrayHasKey('userId', $content, 'Get User Id after successful registration');
    }

    /**
     * Test bad registration user with invalid email.
     *
     * Expects:
     * - HTTP 400 status code
     */
    public function testRegisterUserWithInvalidEmail(): void
    {
        $this->httpClient->request(
            'POST',
            $this->apiRegisterEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'wrong_email', 'password' => $this->testPassword, 'password_confirmation' => $this->testPassword])
        );

        $response = $this->httpClient->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), 'Register should return HTTP 400 on invalid email');
    }

    /**
     * Test bad registration user with invalid password.
     *
     * Expects:
     * - HTTP 400 status code
     */
    public function testRegisterUserWithInvalidPassword(): void
    {
        $this->httpClient->request(
            'POST',
            $this->apiRegisterEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $this->testRegistrationEmail, 'password' => '1234567', 'password_confirmation' => '1234567'])
        );

        $response = $this->httpClient->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), 'Register should return HTTP 400 on invalid password');
    }

    /**
     * Test bad registration user with mismatching password.
     *
     * Expects:
     * - HTTP 400 status code
     */
    public function testRegisterUserWithPasswordMismatch(): void
    {
        $this->httpClient->request(
            'POST',
            $this->apiRegisterEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $this->testRegistrationEmail, 'password' => $this->testPassword, 'password_confirmation' => 'wrong_password'])
        );

        $response = $this->httpClient->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), 'Register should return HTTP 400 on invalid password mismatch');
    }

    /**
     * Test bad registration user with an existing email.
     *
     * Expects:
     * - HTTP 409 status code
     */
    public function testRegisterUserWithExistingEmail(): void
    {
        $this->httpClient->request(
            'POST',
            $this->apiRegisterEndpoint,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $this->testEmail, 'password' => $this->testPassword, 'password_confirmation' => $this->testPassword])
        );

        $response = $this->httpClient->getResponse();

        $this->assertEquals(409, $response->getStatusCode(), 'Register should return HTTP 409 on invalid existing email');
    }

    /**
     * Tear Down test environment after all tests.
     *
     * Remove test user from the Database.
     */
    protected function tearDown(): void
    {
        // Remove test user created in setUp
        if (isset($this->testUser)) {
            $this->userRepository->delete($this->testUser);
        }

        // Remove registered User used for the test
        $registeredUser = $this->userRepository->findByEmail(new Email($this->testRegistrationEmail));
        if ($registeredUser) {
            $this->userRepository->delete($registeredUser);
        }

        parent::tearDown();
    }
}
