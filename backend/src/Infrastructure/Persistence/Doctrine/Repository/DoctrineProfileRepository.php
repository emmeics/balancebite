<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\User\Entity\Profile;
use App\Domain\User\Repository\ProfileRepositoryInterface;
use App\Domain\User\ValueObject\ProfileId;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProfileRepository implements ProfileRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Profile $profile): void
    {
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }

    public function findById(ProfileId $id): ?Profile
    {
        return $this->entityManager->find(Profile::class, $id->getValue());
    }

    public function findByUserId(UserId $userId): ?Profile
    {
        return $this->entityManager
            ->getRepository(Profile::class)
            ->findOneBy(['userId' => $userId->getValue()]);
    }

    public function delete(Profile $profile): void
    {
        $this->entityManager->remove($profile);
        $this->entityManager->flush();
    }
}