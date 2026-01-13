<?php

namespace App\Application\User\Command;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;

final class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {
    }

    public function handle(RegisterUserCommand $command)
    {
        throw new \RuntimeException('Not implemented yet');
    }
}
