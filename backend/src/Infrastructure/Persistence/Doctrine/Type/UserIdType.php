<?php

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\User\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class UserIdType extends StringType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserId
    {
        if (empty($value)) {
            return null;
        }

        return new UserId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof UserId) {
            return $value->getValue();
        }

        return $value;
    }

    public function getName(): string
    {
        return 'user_id';
    }
}
