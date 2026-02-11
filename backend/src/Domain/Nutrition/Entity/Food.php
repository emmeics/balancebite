<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Entity;

use App\Domain\Nutrition\ValueObject\FoodId;
use App\Domain\Nutrition\ValueObject\FoodSource;
use App\Domain\Nutrition\ValueObject\NutrientInfo;

/**
 * Entity representing a food item from an external nutrition data source.
 *
 * Food is a read-only entity - data comes from external APIs like OpenFoodFacts
 * and is not persisted in our database. The entity is immutable after creation.
 *
 * Identity is determined by the FoodId - two Food objects with the same ID
 * represent the same food item even if other properties differ.
 */
final readonly class Food
{
    private FoodId $id;
    private FoodSource $source;
    private string $name;
    private NutrientInfo $nutrients;

    /**
     * @var array<string>
     */
    private array $allergens;

    private ?string $brand;
    private ?string $imageUrl;
    private ?string $ingredientsText;

    /**
     * @param array<string> $allergens
     */
    public function __construct(
        FoodId $id,
        FoodSource $source,
        string $name,
        NutrientInfo $nutrients,
        array $allergens,
        ?string $brand = null,
        ?string $imageUrl = null,
        ?string $ingredientsText = null,
    ) {
        $this->id = $id;
        $this->source = $source;
        $this->name = $name;
        $this->nutrients = $nutrients;
        $this->allergens = $allergens;
        $this->brand = $brand;
        $this->imageUrl = $imageUrl;
        $this->ingredientsText = $ingredientsText;
    }

    /**
     * Returns the Id of Food.
     *
     * @return FoodId The FoodId of Food
     */
    public function getId(): FoodId
    {
        return $this->id;
    }

    /**
     * Returns the Source of Food.
     *
     * @return FoodSource The FoodSource of Food
     */
    public function getSource(): FoodSource
    {
        return $this->source;
    }

    /**
     * Returns the Name of Food.
     *
     * @return string The Name of Food
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the Nutrients of Food.
     *
     * @return NutrientInfo The NutrientInfo of Food
     */
    public function getNutrients(): NutrientInfo
    {
        return $this->nutrients;
    }

    /**
     * Returns the Allergens of Food.
     *
     * @return array<string> The Array of allergens
     */
    public function getAllergens(): array
    {
        return $this->allergens;
    }

    /**
     * Returns the Brand Name of Food.
     *
     * @return ?string The string of the brand
     */
    public function getBrand(): ?string
    {
        return $this->brand;
    }

    /**
     * Returns the Url of the image of the Food.
     *
     * @return ?string The string of the image url
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * Returns the Ingredients the Food.
     *
     * @return ?string The string of the ingredients
     */
    public function getIngredientsText(): ?string
    {
        return $this->ingredientsText;
    }

    /**
     * Verify if an allergen is contained in the lists of allergens.
     *
     * @param string $allergen to verify
     *
     * @return bool The bool result of the contained allergen
     */
    public function hasAllergen(string $allergen): bool
    {
        return in_array($allergen, $this->allergens, true);
    }

    /**
     * Verify if two Food objects are equal by comparing their IDs.
     *
     * @param Food $other The other Food object to compare with
     *
     * @return bool The bool result of the compare of the id value
     */
    public function equals(Food $other): bool
    {
        return $this->id->getValue() === $other->id->getValue();
    }
}
