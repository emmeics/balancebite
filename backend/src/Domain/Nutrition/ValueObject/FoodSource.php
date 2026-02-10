<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\ValueObject;

/**
 * Enum representing external nutrition data sources.
 *
 * Used to identify which API or database provided the food information.
 * This allows the system to handle different data formats via the Factory pattern.
 */
enum FoodSource: string
{
    case OPEN_FOOD_FACTS = 'openfoodfacts';
}