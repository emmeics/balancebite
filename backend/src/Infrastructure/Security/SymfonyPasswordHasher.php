<?php

namespace App\Infrastructure\Security;

use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\HashedPassword;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as SymfonyPasswordHasherInterface;

class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private SymfonyPasswordHasherInterface $hasher,
    ) {
    }

    public function hash(string $plainPassword): HashedPassword
    {
        $hashedPassword = $this->hasher->hash($plainPassword);

        return new HashedPassword($hashedPassword);
    }

    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool
    {
        return $this->hasher->verify($hashedPassword->getValue(), $plainPassword);
    }
}
