<?php

namespace App\Tests\Integration\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\HashedPassword;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineUserRepositoryTest extends KernelTestCase
{
    private UserRepositoryInterface $repository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->repository = $container->get(UserRepositoryInterface::class);
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

    public function testSaveAndFindById(): void
    {
        $user = $this->createTestUser();
        
        $this->repository->save($user);
        
        $found = $this->repository->findById($user->getId());
        
        $this->assertNotNull($found);
        $this->assertTrue($user->getId()->equals($found->getId()));
    }

    public function testFindByEmail(): void
    {
        $email = 'test@example.com';
        $user = $this->createTestUser($email);
        $this->repository->save($user);
        
        $emailObj = new Email($email);
        $found = $this->repository->findByEmail($emailObj);

        $this->assertNotNull($found);
        $this->assertTrue($user->getId()->equals($found->getId()));
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $uuid = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $found = $this->repository->findById($uuid);

        $this->assertNull($found);
    }

    public function testDelete(): void
    {
        $email = 'test@example.com';
        $user = $this->createTestUser($email);
        
        $this->repository->save($user);
        
        $found = $this->repository->findById($user->getId());

        $this->repository->delete($found);

        $emailObj = new Email($email);
        $foundAfterDelete = $this->repository->findByEmail($emailObj);

        $this->assertNull($foundAfterDelete);
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        
        parent::tearDown();
    }
}