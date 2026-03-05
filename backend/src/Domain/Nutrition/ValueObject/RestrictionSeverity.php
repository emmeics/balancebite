<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\ValueObject;

/**
 * Enum representing the risk of restriction.
 *
 * Used in the Entity Restriction
 */
enum RestrictionSeverity: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
