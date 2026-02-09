<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\ValueObject;

use App\Domain\Nutrition\Exception\InvalidFoodIdException;

/**
 * Value Object representing a unique identifier for a Food item from external nutrition data sources.
 *
 * This ID is typically received from APIs like OpenFoodFacts and is used for querying and caching.
 * The Food entity itself is not persisted in our database - data lives in external sources.
 */
final readonly class FoodId
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ('' === $trimmed) {
            throw new InvalidFoodIdException('Food ID cannot be empty');
        }

        $this->value = $trimmed;
    }

    /**
     * Returns the food identifier as a string.
     *
     * @return string The unique food identifier
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Check if two FoodId are equals or not.
     *
     * @return bool The result of equals
     */
    public function equals(FoodId $other): bool
    {
        return $this->value === $other->getValue();
    }
}
