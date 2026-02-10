<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Nutrition\ValueObject;

use App\Domain\Nutrition\Exception\InvalidNutrientInfoException;
use App\Domain\Nutrition\ValueObject\NutrientInfo;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for NutrientInfo ValueObject.
 *
 * - Test the creation of the object NutrientInfo with valid and invalid datas
 * - Test the methods: getCalories, getProteinsGrams, getCarbsGrams, getFatGrams, getFiberGrams, getSodiumMilligrams
 */
final class NutrientInfoTest extends TestCase
{
    /**
     * Test creation the object with valid datas.
     *
     * Expects:
     * - The object created exist and it's an instance of NutrientInfo
     */
    public function testCanBeCreatedWithValidValues(): void
    {
        $nutrient = new NutrientInfo(
            calories: 100.0,
            proteinGrams: 100.0,
            carbsGrams: 100.0,
            fatGrams: 100.0,
            fiberGrams: 100.0,
            sodiumMilligrams: 100.0
        );

        $this->assertTrue(
            $nutrient instanceof NutrientInfo,
            '$nutritions object should exist'
        );
    }

    /**
     * Test creation the object with invalid calories.
     *
     * Expects:
     * - Exception "InvalidNutritionInfoException" thrown when attempting to create a Nutrition object
     */
    public function testThrowsExceptionForNegativeCalories(): void
    {
        $this->expectException(InvalidNutrientInfoException::class);

        $nutrient = new NutrientInfo(
            calories: -100.0,
            proteinGrams: 100.0,
            carbsGrams: 100.0,
            fatGrams: 100.0,
            fiberGrams: 100.0,
            sodiumMilligrams: 100.0
        );
    }

    /**
     * Test creation the object with invalid protein.
     *
     * Expects:
     * - Exception "InvalidNutritionInfoException" thrown when attempting to create a Nutrition object
     */
    public function testThrowsExceptionForNegativeProtein(): void
    {
        $this->expectException(InvalidNutrientInfoException::class);

        $nutrient = new NutrientInfo(
            calories: 100.0,
            proteinGrams: -5.0,
            carbsGrams: 100.0,
            fatGrams: 100.0,
            fiberGrams: 100.0,
            sodiumMilligrams: 100.0
        );
    }

    /**
     * Test the getters method of Value Object NutritionInfo.
     *
     * Expects:
     * - Except that single method return the value used in the creation of the object
     */
    public function testGettersReturnCorrectValues(): void
    {
        $nutrient = new NutrientInfo(
            calories: 100.0,
            proteinGrams: 90.0,
            carbsGrams: 80.0,
            fatGrams: 70.5,
            fiberGrams: 0.5,
            sodiumMilligrams: 1.0
        );

        $this->assertSame(100.0, $nutrient->getCalories(), 'Should return the correct calories');
        $this->assertSame(90.0, $nutrient->getProteinGrams(), 'Should return the correct proteins grams');
        $this->assertSame(80.0, $nutrient->getCarbsGrams(), 'Should return the correct carbs grams');
        $this->assertSame(70.5, $nutrient->getFatGrams(), 'Should return the correct fat grams');
        $this->assertSame(0.5, $nutrient->getFiberGrams(), 'Should return the correct fiber grams');
        $this->assertSame(1.0, $nutrient->getSodiumMilligrams(), 'Should return the correct sodium milligrams');
    }
}
