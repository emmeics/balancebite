<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Service;

use App\Domain\Nutrition\Entity\Food;

/**
 * Factory interface for creating Food entities from external data sources.
 *
 * This interface abstracts the creation of Food entities from different
 * nutrition APIs (OpenFoodFacts, USDA, etc.), allowing each source to handle
 * its own data format while providing a consistent interface to the application layer.
 *
 * Implementations are responsible for:
 * - Parsing raw API response data
 * - Creating required Value Objects (FoodId, NutrientInfo, FoodSource)
 * - Handling missing or malformed data
 * - Constructing valid Food entities
 */
interface FoodFactoryInterface
{
    /**
     * Create a Food Object from Array.
     *
     * @param array<mixed> $data The results of external source
     *
     * @return Food The Food Entity
     */
    public function createFromArray(array $data): Food;
}
