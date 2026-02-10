<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Exception;

/**
 * Exception thrown when attempting to create a NutrientInfo with invalid values.
 *
 * Invalid cases include:
 * - Negative Values for: Calories, Protein, Carbs, Fat, Fiber, Sodium
 */
final class InvalidNutrientInfoException extends \InvalidArgumentException
{
}
