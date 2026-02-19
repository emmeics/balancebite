<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalService\OpenFoodFacts;

use App\Domain\Nutrition\Entity\Food;
use App\Domain\Nutrition\Exception\InvalidFoodDataException;
use App\Domain\Nutrition\Service\FoodFactoryInterface;
use App\Domain\Nutrition\ValueObject\FoodId;
use App\Domain\Nutrition\ValueObject\FoodSource;
use App\Domain\Nutrition\ValueObject\NutrientInfo;

/**
 * Class stateless that permit to transform a Product (raw data) from OpenFoodFacts APIs to Food Entity.
 */
class OpenFoodFactsFoodFactory implements FoodFactoryInterface
{
    /**
     * Validate and Create a new Food Entity from the Array of DataSource.
     *
     * @param array<string, mixed> $data raw data from OpenFoodFacts APIs
     *
     * @return Food new Food Entity of the Product from OpenFoodFacts APIs
     */
    public function createFromArray(array $data): Food
    {
        $productData = $data;
        // Necessary to cover the response of the search() and the getById()
        if (isset($data['product']) && !empty($data['product'])) {
            $productData = $data['product'];
        }
        $this->validateRawData($data, $productData);

        $foodId = new FoodId($data['code']);
        $foodSource = FoodSource::OPEN_FOOD_FACTS;
        $productName = $productData['product_name'];
        $nutrients = $this->createNutrientInfo($productData);
        $allergens = $this->parseAllergens($productData['allergens_tags'] ?? []);
        $brand = $productData['brands'] ?? null;
        $imageUrl = $productData['image_url'] ?? null;
        $ingredientsText = $productData['ingredients_text'] ?? null;

        return new Food(
            id: $foodId,
            source: $foodSource,
            name: $productName,
            nutrients: $nutrients,
            allergens: $allergens,
            brand: $brand,
            imageUrl: $imageUrl,
            ingredientsText: $ingredientsText
        );
    }

    /**
     * Create the NutrientInfo Entity with the data from OpenFoodFacts API.
     *
     * @param array<string, mixed> $productData array that contains all the product informations
     *
     * @return NutrientInfo Nutrients Entity
     */
    private function createNutrientInfo(array $productData): NutrientInfo
    {
        $nutrients = new NutrientInfo(
            calories: (float) ($productData['nutriments']['energy-kcal_100g'] ?? 0.0),
            proteinGrams: (float) ($productData['nutriments']['proteins_100g'] ?? 0.0),
            carbsGrams: (float) ($productData['nutriments']['carbohydrates_100g'] ?? 0.0),
            fatGrams: (float) ($productData['nutriments']['fat_100g'] ?? 0.0),
            fiberGrams: (float) ($productData['nutriments']['fiber_100g'] ?? 0.0),
            sodiumMilligrams: (float) (($productData['nutriments']['sodium_100g'] ?? 0.0) * 1000)
        );

        return $nutrients;
    }

    /**
     * Parse and clean all the allergens recieved from the data source.
     *
     * @param array<string> $allergens allergens recieved from the data source
     *
     * @return array<string> parsed allergens
     */
    private function parseAllergens(array $allergens): array
    {
        $parsedAllergen = [];
        foreach ($allergens as $allergen) {
            $split = explode(':', $allergen);
            $parsedAllergen[] = $split[1];
        }

        return $parsedAllergen;
    }

    /**
     * Validate all Raw Data recieved:
     * - Required Fields
     * - Not Valid Fields
     *
     * @param array<string, mixed> $data        all data recieved from the data source
     * @param array<string, mixed> $productData product data recieved from the data source
     */
    private function validateRawData(array $data, array $productData): void
    {
        if (!isset($data['code']) || empty($data['code'])) {
            throw InvalidFoodDataException::missingRequiredFields('NOT_FOUND', ['code']);
        }

        $this->validateRequiredProductFields($data['code'], $productData);

        $notValidFields = [];
        if (!empty($productData['image_url'])) {
            if (!filter_var($productData['image_url'], FILTER_VALIDATE_URL)) {
                $field['name'] = 'image_url';
                $field['error'] = 'This field contain an invalid Url';
                $notValidFields[] = $field;
            }
        }

        if (!empty($notValidFields)) {
            throw InvalidFoodDataException::notValidFields($data['code'], $notValidFields);
        }
    }

    /**
     * Validate required fields.
     *
     * @param string               $productCode the code of the product
     * @param array<string, mixed> $productData all data recieved from the data source
     */
    private function validateRequiredProductFields(string $productCode, array $productData): void
    {
        $requiredFields = [
            'product_name',
            'nutriments',
        ];

        $requiredNutrimentsFields = [
            'energy-kcal_100g',
            'proteins_100g',
            'carbohydrates_100g',
            'fat_100g',
            'fiber_100g',
            'sodium_100g',
        ];

        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($productData[$field])) {
                $missingFields[] = $field;
            }
        }

        foreach ($requiredNutrimentsFields as $field) {
            if (!isset($productData['nutriments'][$field])) {
                $missingFields[] = 'nutriments-'.$field;
            }
        }

        if (!empty($missingFields)) {
            throw InvalidFoodDataException::missingRequiredFields($productCode, $missingFields);
        }
    }
}
