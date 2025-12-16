<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidEmailException;

final class Email
{
    private string $value;

    /**
     * @throws InvalidEmailException When email format is invalid
    */
    public function __construct(string $value)
    {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailException::withEmail($value);
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function getDomain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }
}