<?php

namespace App\Domain\User\Exception;

use InvalidArgumentException;

final class InvalidPasswordException extends InvalidArgumentException
{
    public static function becauseEmpty(): self
    {
        return new self('Password hash cannot be empty.');    
    }
}