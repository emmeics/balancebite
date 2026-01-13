<?php

namespace App\Application\User\Command;

final class RegisterUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordConfirmation,
    ) {
    }
}
