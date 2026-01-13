<?php

namespace App\Application\User\Command;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;

final class RegisterUserHandler
{
    public function __construct(
        UserRepositoryInterface $userRepository,
        PasswordHasherInterface $passwordHasher
    ) {}

    public function handle(RegisterUserCommand $command)
    {
        
    }
}