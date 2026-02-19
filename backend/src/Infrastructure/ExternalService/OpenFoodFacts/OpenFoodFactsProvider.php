<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalService\OpenFoodFacts;

use App\Domain\Nutrition\Entity\Food;
use App\Domain\Nutrition\Exception\InvalidFoodDataException;
use App\Domain\Nutrition\Repository\FoodRepositoryInterface;
use App\Domain\Nutrition\Service\FoodFactoryInterface;
use App\Domain\Nutrition\ValueObject\FoodId;

/**
 * Provider that read data from OpenFoodFacts API and return it in a Food Entities.
 */
final class OpenFoodFactsProvider implements FoodRepositoryInterface
{
    public function __construct(
        private OpenFoodFactsClient $client,
        private FoodFactoryInterface $factory,
    ) {
    }

    /**
     * Find a Food by its unique identifier.
     *
     * @param FoodId $id The food identifier (barcode)
     *
     * @return Food|null The Food entity if found, null if not found in external API
     *
     * @throws InvalidFoodDataException If external API returns invalid/incomplete data
     */
    public function findById(FoodId $id): ?Food
    {
        $data = $this->client->getById($id->getValue());

        if (empty($data)) {
            return null;
        }

        return $this->factory->createFromArray($data);
    }
}
