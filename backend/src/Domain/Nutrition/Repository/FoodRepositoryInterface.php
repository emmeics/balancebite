<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Repository;

use App\Domain\Nutrition\Entity\Food;
use App\Domain\Nutrition\ValueObject\FoodId;

/**
 * Repository Interface to permit to create Nutrition Data Source Class that use a standard.
 */
interface FoodRepositoryInterface
{
    /**
     * Find a Food by FoodId.
     *
     * @param FoodId $id The food identifier
     *
     * @return Food|null The Food found, or null if not exists
     */
    public function findById(FoodId $id): ?Food;
}
