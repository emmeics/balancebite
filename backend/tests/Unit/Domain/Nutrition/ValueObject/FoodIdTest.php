<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Nutrition\ValueObject;

use App\Domain\Nutrition\Exception\InvalidFoodIdException;
use App\Domain\Nutrition\ValueObject\FoodId;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for FoodId ValueObject.
 *
 * - Test the creation of the Object with a valid and invalid ID
 * - Test the methods: getValue and equals
 */
final class FoodIdTest extends TestCase
{
    /**
     * Test create the object with a valid ID.
     *
     * Expects:
     * - The example ID is the same of the Object created
     */
    public function testCanBeCreatedWithValidId(): void
    {
        // Arrange
        $validId = '1234567891234';

        // Act
        $foodId = new FoodId($validId);

        // Assert
        $this->assertSame($validId, $foodId->getValue(), 'getValue() should return the exact ID string provided in constructor');
    }

    /**
     * Test create the object with an empty ID.
     *
     * Expects:
     * - Exception "InvalidFoodIdException" thrown when attempting to create a FoodId object
     */
    public function testCannotBeCreatedWithEmptyId(): void
    {
        // Arrange
        $invalidId = '';

        // Assert
        $this->expectException(InvalidFoodIdException::class);
        $this->expectExceptionMessage('Food ID cannot be empty');

        // Act
        $foodId = new FoodId($invalidId);
    }

    /**
     * Test create the object with a whitespace ID.
     *
     * Expects:
     * - Exception "InvalidFoodIdException" thrown when attempting to create a FoodId object
     */
    public function testCannotBeCreatedWithWhitespaceOnlyId(): void
    {
        // Arrange
        $invalidId = '    ';

        // Assert
        $this->expectException(InvalidFoodIdException::class);
        $this->expectExceptionMessage('Food ID cannot be empty');

        // Act
        $foodId = new FoodId($invalidId);
    }

    /**
     * Test verify that two object with the same Id are equals.
     *
     * Expects:
     * - Except true by the result of the method "equals"
     */
    public function testEqualsTrueForSameValue(): void
    {
        $validId = '1234567891234';

        $foodId1 = new FoodId($validId);
        $foodId2 = new FoodId($validId);

        $this->assertTrue($foodId1->equals($foodId2), 'The tow FoodId should be equals');
    }

    /**
     * Test verify that two object with a different Id are not equals.
     *
     * Expects:
     * - Except false by the result of the method "equals"
     */
    public function testEqualsFalseForDifferentValue(): void
    {
        $foodId1 = new FoodId('1234567891234');
        $foodId2 = new FoodId('1234567891230');

        $this->assertFalse($foodId1->equals($foodId2), 'The tow FoodId should not be equals');
    }
}
