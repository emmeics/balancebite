<?php

namespace App\Domain\User\Exception;

final class InvalidEmailException extends \InvalidArgumentException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('The email "%s" is not valid.', $email));
    }
}
