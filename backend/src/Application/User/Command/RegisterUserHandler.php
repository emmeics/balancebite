<?php

namespace App\Application\User\Command;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\InvalidPasswordException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;

final class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {
    }

    public function handle(RegisterUserCommand $command): UserId
    {
        // 1. Validate passwords match
        if ($command->password !== $command->passwordConfirmation) {
            throw InvalidPasswordException::passwordsDoNotMatch();
        }

        // 2. Validate password length
        if (strlen($command->password) < 8) {
            throw InvalidPasswordException::tooShort(8);
        }

        // 3. Create Email (may throw InvalidEmailException)
        $email = new Email($command->email);

        // 4. Check email not already registered
        if ($this->userRepository->findByEmail($email) !== null) {
            throw new \DomainException('Email already registered');
        }

        // 5. Hash password
        $hashedPassword = $this->passwordHasher->hash($command->password);

        // 6. Create and save user
        $user = User::register($email, $hashedPassword);
        $this->userRepository->save($user);

        return $user->getId();
    }
}
