<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\ValueObject;

/**
 * Enum representing types of food restriction.
 *
 * Used in the Entity Restriction
 */
enum RestrictionType: string
{
    case ALLERGEN = 'allergen';
    case INTOLERANCE = 'intolerance';
    case MEDICAL_CONDITION = 'medical_condition';
    case EATING_DISORDER = 'eating_disorder';
    case ETHICAL = 'ethical';
}
