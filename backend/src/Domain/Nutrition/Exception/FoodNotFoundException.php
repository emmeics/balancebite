<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Exception;

/**
 * Exception thrown when the food is not found with the FoodId.
 *
 * Invalid cases include:
 * - Food not found
 */
final class FoodNotFoundException extends \RuntimeException
{
}
