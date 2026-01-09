<?php

namespace App\Domain\User\ValueObject;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\Exception\InvalidArgumentException;

final class ProfileId
{
    private UUid $value;

    public function __construct(string $value)
    {
        if(!UUid::isValid($value)) {
            throw new InvalidArgumentException('Invalid UUid:'.$value);
        }
        $this->value = UUid::fromString($value);
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