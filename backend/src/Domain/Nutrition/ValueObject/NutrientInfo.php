<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\ValueObject;

use App\Domain\Nutrition\Exception\InvalidNutrientInfoException;

/**
 * Value Object representing a collection of nutrients for a Food.
 *
 * This nutrients are typically received from APIs like OpenFoodFacts.
 * The Food entity itself is not persisted in our database - data lives in external sources.
 */
final readonly class NutrientInfo
{
    public function __construct(
        private float $calories,
        private float $proteinGrams,
        private float $carbsGrams,
        private float $fatGrams,
        private float $fiberGrams,
        private float $sodiumMilligrams,
    ) {
        if ($this->calories < 0 || $this->proteinGrams < 0 || $this->carbsGrams < 0 || $this->fatGrams < 0 || $this->fiberGrams < 0 || $this->sodiumMilligrams < 0) {
            throw new InvalidNutrientInfoException('Nutrient values cannot be negative');
        }
    }

    /**
     * Returns the calories.
     *
     * @return float The calories
     */
    public function getCalories(): float
    {
        return $this->calories;
    }

    /**
     * Returns the protein grams.
     *
     * @return float The protein grams
     */
    public function getProteinGrams(): float
    {
        return $this->proteinGrams;
    }

    /**
     * Returns the carbs grams.
     *
     * @return float The carbs grams
     */
    public function getCarbsGrams(): float
    {
        return $this->carbsGrams;
    }

    /**
     * Returns the fat grams.
     *
     * @return float The fat grams
     */
    public function getFatGrams(): float
    {
        return $this->fatGrams;
    }

    /**
     * Returns the fiber grams.
     *
     * @return float The fiber grams
     */
    public function getFiberGrams(): float
    {
        return $this->fiberGrams;
    }

    /**
     * Returns the sodium milligrams.
     *
     * @return float The sodium milligrams
     */
    public function getSodiumMilligrams(): float
    {
        return $this->sodiumMilligrams;
    }
}
