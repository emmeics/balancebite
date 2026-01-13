<?php

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\User\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EmailType extends StringType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        if (empty($value)) {
            return null;
        }

        return new Email($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof Email) {
            return $value->getValue();
        }

        return $value;
    }

    public function getName(): string
    {
        return 'email_address';
    }
}
