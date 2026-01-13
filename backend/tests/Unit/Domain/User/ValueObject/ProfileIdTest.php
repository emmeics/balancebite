<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\ProfileId;
use PHPUnit\Framework\TestCase;

class ProfileIdTest extends TestCase
{
    public function testCanBeCreatedWithValidUuid(): void
    {
        // Arrange
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';

        // Act
        $profileId = new ProfileId($validUuid);

        // Assert
        $this->assertSame($validUuid, $profileId->getValue());
    }

    public function testCannotBeCreatedWithInvalidUuid(): void
    {
        $invalidUUid = 'invalid-uuid';

        $this->expectException(\InvalidArgumentException::class);

        $profileId = new ProfileId($invalidUUid);
    }

    public function testGenerateCreatesValidUserId(): void
    {
        $generatedProfileId = ProfileId::generate();

        $this->assertNotEmpty($generatedProfileId->getValue());
    }

    public function testEqualsTrueForSameValue(): void
    {
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';

        $profileId1 = new ProfileId($validUuid);
        $profileId2 = new ProfileId($validUuid);

        $this->assertTrue($profileId1->equals($profileId2));
    }

    public function testEqualsFalseForDifferentValue(): void
    {
        $profileId1 = new ProfileId('550e8400-e29b-41d4-a716-446655440000');
        $profileId2 = new ProfileId('550e8400-e29b-41d4-a716-446655440001');

        $this->assertFalse($profileId1->equals($profileId2));
    }
}
