<?php

namespace App\Tests\Integration\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\User\Entity\Profile;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\ProfileRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\ActivityLevel;
use App\Domain\User\ValueObject\DietaryGoal;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Gender;
use App\Domain\User\ValueObject\HashedPassword;
use App\Domain\User\ValueObject\HealthCondition;
use App\Domain\User\ValueObject\ProfileId;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineProfileRepositoryTest extends KernelTestCase
{
    private ProfileRepositoryInterface $profileRepository;
    private UserRepositoryInterface $userRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->profileRepository = $container->get(ProfileRepositoryInterface::class);
        $this->userRepository = $container->get(UserRepositoryInterface::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->entityManager->beginTransaction();
    }

    private function createTestUser(string $email = 'test@example.com'): User
    {
        return User::register(
            new Email($email),
            new HashedPassword('hashed_password_123')
        );
    }

    private function createTestProfile(User $user): Profile
    {
        return Profile::create(
            $user->getId(),
            'John',
            'Doe',
            new DateTimeImmutable('now'),
            Gender::from('male'),
            190,
            80.0,
            ActivityLevel::from('sedentary'),
            DietaryGoal::from('maintain')
        );
    }

    public function testSaveAndFindById(): void
    {
        // Create User
        $user = $this->createTestUser();
        $this->userRepository->save($user);
        
        $profile = $this->createTestProfile($user);
        $this->profileRepository->save($profile);

        $found = $this->profileRepository->findById($profile->getProfileId());
        
        $this->assertNotNull($found);
        $this->assertTrue($profile->getProfileId()->equals($found->getProfileId()));
    }

    public function testSaveAndAddHealthConditions(): void
    {
        // Create User
        $user = $this->createTestUser();
        $this->userRepository->save($user);
        
        $profile = $this->createTestProfile($user);
        $this->profileRepository->save($profile);
        
        $healthConditions = [
            HealthCondition::from('ibs'),
            HealthCondition::from('reflux')
        ];

        $profile->addHealthCondition($healthConditions[0]);
        $profile->addHealthCondition($healthConditions[1]);

        $this->profileRepository->save($profile);

        $foundProfile = $this->profileRepository->findById($profile->getProfileId());
        $foundedHealthConditions = $foundProfile->getHealthConditions();

        $this->assertContains($healthConditions[0], $foundedHealthConditions);
        $this->assertContains($healthConditions[1], $foundedHealthConditions);
    }

    public function testFindByUserId(): void
    {
        // Create User
        $user = $this->createTestUser();
        $this->userRepository->save($user);
        
        $profile = $this->createTestProfile($user);
        $this->profileRepository->save($profile);
        
        $found = $this->profileRepository->findByUserId($profile->getUserId());

        $this->assertNotNull($found);
        $this->assertTrue($profile->getProfileId()->equals($found->getProfileId()));
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $uuid = new ProfileId('550e8400-e29b-41d4-a716-446655440000');
        $found = $this->profileRepository->findById($uuid);

        $this->assertNull($found);
    }

    public function testDelete(): void
    {
        // Create User
        $user = $this->createTestUser();
        $this->userRepository->save($user);
        
        $profile = $this->createTestProfile($user);
        $this->profileRepository->save($profile);
        
        $found = $this->profileRepository->findByUserId($profile->getUserId());

        $this->profileRepository->delete($found);

        $foundAfterDelete = $this->profileRepository->findByUserId($user->getId());

        $this->assertNull($foundAfterDelete);
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        
        parent::tearDown();
    }
}