<?php

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\User\ValueObject\HealthCondition;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class HealthConditionsType extends JsonType
{
    /**
     * @return array<HealthCondition>|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        $values = parent::convertToPHPValue($value, $platform);

        if (!is_array($values)) {
            return [];
        }

        $healthConditions = [];
        foreach ($values as $value) {
            $healthCondition = HealthCondition::from($value);
            if ($healthCondition instanceof HealthCondition) {
                $healthConditions[] = $healthCondition;
            }
        }

        return $healthConditions;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!is_array($value)) {
            return null;
        }

        $healthConditions = [];
        foreach ($value as $healthCondition) {
            if ($healthCondition instanceof HealthCondition) {
                $healthConditions[] = $healthCondition->value;
            }
        }

        return parent::convertToDatabaseValue($healthConditions, $platform);
    }

    public function getName(): string
    {
        return 'health_conditions';
    }
}
