<?php

namespace App\Tests\Integration\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\HashedPassword;
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

    public function testSaveAndFindById(): void
    {
        $user = User::register(
            new Email('test@example.com'),
            new HashedPassword('hashed123')
        );
        
        $this->repository->save($user);
        
        $found = $this->repository->findById($user->getId());
        
        $this->assertNotNull($found);
        $this->assertTrue($user->getId()->equals($found->getId()));
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        
        parent::tearDown();
    }
}