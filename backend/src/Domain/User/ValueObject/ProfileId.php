<?php

namespace App\Domain\User\ValueObject;

use Symfony\Component\Uid\Exception\InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

final class ProfileId
{
    private Uuid $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException('Invalid UUid:'.$value);
        }
        $this->value = Uuid::fromString($value);
    }

    public function getValue(): string
    {
        return $this->value->toString();
    }

    public static function generate(): self
    {
        return new self(Uuid::v6()->toString());
    }

    public function equals(self $other): bool
    {
        return $this->value->toString() === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value->toString();
    }
}
