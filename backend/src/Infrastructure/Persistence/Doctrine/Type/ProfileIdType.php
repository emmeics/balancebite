<?php

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\User\ValueObject\ProfileId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ProfileIdType extends StringType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProfileId
    {
        if (empty($value)) {
            return null;
        }

        return new ProfileId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof ProfileId) {
            return $value->getValue();
        }

        return $value;
    }

    public function getName(): string
    {
        return 'profile_id';
    }
}
