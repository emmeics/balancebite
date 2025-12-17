<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testCanBeCreatedWithValidUuid(): void
    {
        // Arrange
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';

        // Act
        $userId = new UserId($validUuid);

        // Assert
        $this->assertSame($validUuid, $userId->getValue());
    }

    public function testCannotBeCreatedWithInvalidUuid(): void
    {
        $invalidUUid = 'invalid-uuid';

        $this->expectException(InvalidArgumentException::class);

        $userId = new UserId($invalidUUid);
    }

    public function testGenerateCreatesValidUserId(): void
    {
        $generatedUserId = UserId::generate();

        $this->assertNotEmpty($generatedUserId->getValue());
    }

    public function testEqualsTrueForSameValue(): void
    {
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';

        $userId1 = new UserId($validUuid);
        $userId2 = new UserId($validUuid);

        $this->assertTrue($userId1->equals($userId2));
    }

    public function testEqualsFalseForDifferentValue(): void
    {
        $userId1 = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $userId2 = new UserId('550e8400-e29b-41d4-a716-446655440001');

        $this->assertFalse($userId1->equals($userId2));
    }
}
