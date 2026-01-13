<?php

namespace App\Domain\User\Exception;

final class InvalidPasswordException extends \InvalidArgumentException
{
    public static function becauseEmpty(): self
    {
        return new self('Password hash cannot be empty.');
    }

    public static function tooShort(int $minLength): self
    {
        return new self(sprintf('Password must be at least %d characters long', $minLength));
    }

    public static function passwordsDoNotMatch(): self
    {
        return new self('Password and password confirmation do not match');
    }
}
