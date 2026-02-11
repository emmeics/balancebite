<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Nutrition\Entity;

use App\Domain\Nutrition\Entity\Food;
use App\Domain\Nutrition\ValueObject\FoodId;
use App\Domain\Nutrition\ValueObject\FoodSource;
use App\Domain\Nutrition\ValueObject\NutrientInfo;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Food Class.
 *
 * - Test the creation of the object Food with valid and invalid datas
 * - Test the methods: getters, hasAllergen, hasNoAllergen, equals
 */
final class FoodTest extends TestCase
{
    private string $foodIdStr = '123456789123';
    private float $calories = 100.0;
    private float $proteinGrams = 90.0;
    private float $carbsGrams = 80.0;
    private float $fatGrams = 70.5;
    private float $fiberGrams = 0.5;
    private float $sodiumMilligrams = 1.0;
    private string $foodSource = 'openfoodfacts';
    private string $foodName = 'Nutella';
    private array $allergens = ['gluten', 'lactose'];
    private string $brand = 'Ferrero';
    private string $imageUrl = 'https://image_url.com/nutella.jpg';
    private string $ingredientsText = 'hazelnuts';
    private FoodId $foodId;
    private NutrientInfo $nutrient;
    private FoodSource $foodSourceEnum;

    /**
     * Helper method to create a Food Object.
     *
     * @param bool withAllParameters set the creation with all parameters or not
     *
     * @return Food Istance of Food Object
     */
    private function createFoodObject(bool $withAllParameters = false): Food
    {
        $this->foodId = new FoodId($this->foodIdStr);
        $this->nutrient = new NutrientInfo(
            calories: $this->calories,
            proteinGrams: $this->proteinGrams,
            carbsGrams: $this->carbsGrams,
            fatGrams: $this->fatGrams,
            fiberGrams: $this->fiberGrams,
            sodiumMilligrams: $this->sodiumMilligrams
        );
        $this->foodSourceEnum = FoodSource::from($this->foodSource);

        $food = new Food(
            id: $this->foodId,
            source: $this->foodSourceEnum,
            name: $this->foodName,
            nutrients: $this->nutrient,
            allergens: $this->allergens,
            brand: $withAllParameters ? $this->brand : null,
            imageUrl: $withAllParameters ? $this->imageUrl : null,
            ingredientsText: $withAllParameters ? $this->ingredientsText : null
        );

        return $food;
    }

    /**
     * Test creation the object with all parameters.
     *
     * Expects:
     * - The object created exist and it's an instance of Food
     * - The parameters of the created object are the same as the parameters inserted
     */
    public function testCanBeCreatedWithAllPropertiesAndGettersWork(): void
    {
        $food = $this->createFoodObject(true);

        $this->assertTrue(
            $food instanceof Food,
            'Object $food should exist'
        );
        $this->assertSame($this->foodId->getValue(), $food->getId()->getValue(), 'The id should be the same');
        $this->assertSame($this->foodSourceEnum->value, $food->getSource()->value, 'The value of food source should be the same');
        $this->assertSame($this->foodName, $food->getName(), 'The value of the name should be the same');
        $this->assertSame($this->nutrient, $food->getNutrients(), 'The object nutrient should be the same');
        $this->assertContains($this->allergens[0], $food->getAllergens(), 'The value should be contained in the array');
        $this->assertContains($this->allergens[1], $food->getAllergens(), 'The value should be contained in the array');
        $this->assertSame($this->brand, $food->getBrand(), 'The value of the brand should be the same');
        $this->assertSame($this->imageUrl, $food->getImageUrl(), 'The value of the url should be the same');
        $this->assertSame($this->ingredientsText, $food->getIngredientsText(), 'The value of the ingredients should be the same');
    }

    /**
     * Test creation the object with minimal parameters.
     *
     * Expects:
     * - The object created exist and it's an instance of Food
     * - The parameters of the created object are the same as the parameters inserted, the missing parameters are null
     */
    public function testCanBeCreatedWithMinimalProperties(): void
    {
        $food = $this->createFoodObject();

        $this->assertTrue(
            $food instanceof Food,
            'Object $food should exist'
        );
        $this->assertSame($this->foodId->getValue(), $food->getId()->getValue(), 'The id should be the same');
        $this->assertSame($this->foodSourceEnum->value, $food->getSource()->value, 'The value of food source should be the same');
        $this->assertSame($this->foodName, $food->getName(), 'The value of the name should be the same');
        $this->assertSame($this->nutrient, $food->getNutrients(), 'The object nutrient should be the same');
        $this->assertContains($this->allergens[0], $food->getAllergens(), 'The value should be contained in the array');
        $this->assertContains($this->allergens[1], $food->getAllergens(), 'The value should be contained in the array');
        $this->assertNull($food->getBrand(), 'The value of the brand should be null');
        $this->assertNull($food->getImageUrl(), 'The value of the url should be null');
        $this->assertNull($food->getIngredientsText(), 'The value of the ingredients should be null');
    }

    /**
     * Test the correct presence of allergen with the method "hasAllergen".
     *
     * Expects:
     * - The return of method is true
     */
    public function testHasAllergenReturnsTrueWhenPresent(): void
    {
        $food = $this->createFoodObject(true);

        $allergen = 'gluten';
        $this->assertTrue($food->hasAllergen($allergen), 'Should contain the allergen');
    }

    /**
     * Test the correct no presence of allergen with the method "hasAllergen".
     *
     * Expects:
     * - The return of method is true
     */
    public function testHasAllergenReturnsFalseWhenNotPresent(): void
    {
        $food = $this->createFoodObject(true);

        $allergen = 'fish';
        $this->assertFalse($food->hasAllergen($allergen), 'Should not contain the allergen');
    }

    /**
     * Test the equality of two object based on the same id.
     *
     * Expects:
     * - The two objects have the same id
     */
    public function testEqualityBasedOnId(): void
    {
        $food1 = $this->createFoodObject(true);
        $food2 = $this->createFoodObject(false);

        $this->assertSame($food1->getId()->getValue(), $food2->getId()->getValue(), 'Should have the same id');
    }
}
