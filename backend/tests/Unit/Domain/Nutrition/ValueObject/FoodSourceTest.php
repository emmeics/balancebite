<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Nutrition\ValueObject;

use App\Domain\Nutrition\ValueObject\FoodSource;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for FoodSource Enum ValueObject.
 *
 * - Test the exist of FoodSource Enum Type
 */
final class FoodSourceTest extends TestCase
{
    /**
     * Test the existing of the Class.
     *
     * Expects:
     * - The existing of the class FoodSource return true
     */
    public function testOpenFoodFactsCaseExists(): void
    {
        $this->assertTrue(
            FoodSource::OPEN_FOOD_FACTS instanceof FoodSource,
            'OPEN_FOOD_FACTS case should exist'
        );
    }

    /**
     * Test the correct value for the Enum.
     *
     * Expects:
     * - The existing of the correct value for the FoodSource enum
     */
    public function testOpenFoodFactsHasCorrectValue(): void
    {
        $this->assertSame(
            'openfoodfacts',
            FoodSource::OPEN_FOOD_FACTS->value,
            'OPEN_FOOD_FACTS should have value "openfoodfacts"'
        );
    }

    /**
     * Test the create from a valid string.
     *
     * Expects:
     * - The created enum from a string is the same as the Enum case
     */
    public function testCanCreateFromValidString(): void
    {
        $source = FoodSource::from('openfoodfacts');
        $this->assertSame(FoodSource::OPEN_FOOD_FACTS, $source, 'from() should return the correct enum case for valid string value');
    }

    /**
     * Test the creation from an invalid string.
     *
     * Expects:
     * - Exception "ValueError" thrown when attempting to create a FoodSource object
     */
    public function testThrowsExceptionForInvalidString(): void
    {
        $this->expectException(\ValueError::class);
        FoodSource::from('invalid');
    }
}
