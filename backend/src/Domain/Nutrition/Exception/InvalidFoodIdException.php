<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Exception;

/**
 * Exception thrown when attempting to create a FoodId with an invalid value.
 *
 * Invalid cases include:
 * - Empty string
 * - String containing only whitespace
 */
final class InvalidFoodIdException extends \InvalidArgumentException
{
}
