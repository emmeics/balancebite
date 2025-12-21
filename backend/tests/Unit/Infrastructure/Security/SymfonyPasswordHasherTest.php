<?php

namespace App\Tests\Unit\Infrastructure\Security;

use App\Domain\User\ValueObject\HashedPassword;
use App\Infrastructure\Security\SymfonyPasswordHasher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as SymfonyPasswordHasherInterface;

class SymfonyPasswordHasherTest extends TestCase
{
    public function testHashReturnsHashedPassword(): void
    {
        $symfonyHasher = $this->createMock(SymfonyPasswordHasherInterface::class);
        $symfonyHasher->expects($this->once())
            ->method('hash')
            ->with('plain_password')
            ->willReturn('hashed_password_value');

        $hasher = new SymfonyPasswordHasher($symfonyHasher);

        $result = $hasher->hash('plain_password');

        $this->assertInstanceOf(HashedPassword::class, $result);
        $this->assertSame('hashed_password_value', $result->getValue());
    }
    
    public function testVerifyReturnsTrueForValidPassword(): void
    {
        // Arrange
        $symfonyHasher = $this->createMock(SymfonyPasswordHasherInterface::class);
        $symfonyHasher->expects($this->once())
            ->method('verify')
            ->with('hashed_password_value', 'plain_password')
            ->willReturn(true);

        $hasher = new SymfonyPasswordHasher($symfonyHasher);
        $hashedPassword = new HashedPassword('hashed_password_value');

        // Act
        $result = $hasher->verify('plain_password', $hashedPassword);

        // Assert
        $this->assertTrue($result);
    }

    public function testVerifyReturnsFalseForInvalidPassword(): void
    {
        // Arrange
        $symfonyHasher = $this->createMock(SymfonyPasswordHasherInterface::class);
        $symfonyHasher->expects($this->once())
            ->method('verify')
            ->with('hashed_password_value', 'wrong_password')
            ->willReturn(false);

        $hasher = new SymfonyPasswordHasher($symfonyHasher);
        $hashedPassword = new HashedPassword('hashed_password_value');

        // Act
        $result = $hasher->verify('wrong_password', $hashedPassword);

        // Assert
        $this->assertFalse($result);
    }
}