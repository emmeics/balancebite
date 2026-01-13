<?php

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\User\ValueObject\HashedPassword;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class HashedPasswordType extends StringType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?HashedPassword
    {
        if (empty($value)) {
            return null;
        }

        return new HashedPassword($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof HashedPassword) {
            return $value->getValue();
        }

        return $value;
    }

    public function getName(): string
    {
        return 'hashed_password';
    }
}
