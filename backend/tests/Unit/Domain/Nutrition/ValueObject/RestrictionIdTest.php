<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Nutrition\ValueObject;

use App\Domain\Nutrition\ValueObject\RestrictionId;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for RestrictionIdTest ValueObject.
 *
 * - Test the creation of the object RestrictionIdTest with valid and invalid datas
 */
class RestrictionIdTest extends TestCase
{
    /**
     * Verify is possibile to create the entity with a valid UUid.
     *
     * Expects:
     * - The UUid used is the same returned by getValue method
     */
    public function testCanBeCreatedWithValidUuid(): void
    {
        // Arrange
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';

        // Act
        $userId = new RestrictionId($validUuid);

        // Assert
        $this->assertSame($validUuid, $userId->getValue(), 'The UUid string should be the same');
    }

    /**
     * Verify is possibile to create the entity with a invalid UUid.
     *
     * Expects:
     * - The Exception is trown
     */
    public function testCannotBeCreatedWithInvalidUuid(): void
    {
        $invalidUUid = 'invalid-uuid';

        $this->expectException(\InvalidArgumentException::class);

        $userId = new RestrictionId($invalidUUid);
    }

    /**
     * Verify if method generate return a valid UUid.
     *
     * Expects:
     * - The return value should be not empty
     */
    public function testGenerateCreatesValidRestrictionId(): void
    {
        $generatedRestrictionId = RestrictionId::generate();

        $this->assertNotEmpty($generatedRestrictionId->getValue(), 'Should be not empty');
    }

    /**
     * Verify if two Entities with the same UUid are equals.
     *
     * Expects:
     * - The two object are equals
     */
    public function testEqualsTrueForSameValue(): void
    {
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';

        $restrictionId1 = new RestrictionId($validUuid);
        $restrictionId2 = new RestrictionId($validUuid);

        $this->assertTrue($restrictionId1->equals($restrictionId2), 'Should be equals');
    }

    /**
     * Verify if two Entities with a different UUid are equals.
     *
     * Expects:
     * - The two object are not equals
     */
    public function testEqualsFalseForDifferentValue(): void
    {
        $restrictionId1 = new RestrictionId('550e8400-e29b-41d4-a716-446655440000');
        $restrictionId2 = new RestrictionId('6ba7b810-9dad-11d1-80b4-00c04fd430c8');

        $this->assertFalse($restrictionId1->equals($restrictionId2), 'Should be not equals');
    }
}
