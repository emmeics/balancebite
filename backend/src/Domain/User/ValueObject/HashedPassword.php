<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidPasswordException;

final class HashedPassword
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw InvalidPasswordException::becauseEmpty();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
