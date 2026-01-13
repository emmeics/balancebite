<?php

namespace App\Domain\User\Service;

use App\Domain\User\ValueObject\HashedPassword;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): HashedPassword;

    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool;
}
