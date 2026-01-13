<?php

namespace App\Tests\Unit\Domain\User\Entity;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\HashedPassword;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCanRegisterNewUser(): void
    {
        $email = new Email('test@mail.com');
        $passwd = new HashedPassword('HashedPassword');

        $user = User::register($email, $passwd);

        $this->assertSame($email->getValue(), $user->getEmail()->getValue());
        $this->assertSame($passwd->getValue(), $user->getPassword()->getValue());
        $this->assertNotEmpty($user->getId()->getValue());
        $this->assertNotEmpty($user->getCreatedAt());
        $this->assertNotEmpty($user->getUpdatedAt());
    }

    public function testCanReconstituteUserAndGetters(): void
    {
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $email = new Email('test@mail.com');
        $passwd = new HashedPassword('HashedPassword');
        $createdAt = new \DateTimeImmutable('now');
        $updatedAt = new \DateTimeImmutable('now');

        $user = User::reconstitute(
            $userId,
            $email,
            $passwd,
            $createdAt,
            $updatedAt
        );

        $this->assertSame($email->getValue(), $user->getEmail()->getValue());
        $this->assertSame($passwd->getValue(), $user->getPassword()->getValue());
        $this->assertSame($userId->getValue(), $user->getId()->getValue());
        $this->assertSame($createdAt, $user->getCreatedAt());
        $this->assertSame($updatedAt, $user->getUpdatedAt());
    }

    public function testChangeEmail(): void
    {
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $email = new Email('test@mail.com');
        $passwd = new HashedPassword('HashedPassword');
        $createdAt = new \DateTimeImmutable('now');
        $updatedAt = new \DateTimeImmutable('now');

        $user = User::reconstitute(
            $userId,
            $email,
            $passwd,
            $createdAt,
            $updatedAt
        );

        $this->assertSame($email->getValue(), $user->getEmail()->getValue());

        $originalUpdatedAt = $user->getUpdatedAt();
        $newMail = new Email('newtest@mail.com');

        $user->changeEmail($newMail);

        $this->assertSame($newMail->getValue(), $user->getEmail()->getValue());
        $this->assertNotEquals($originalUpdatedAt, $user->getUpdatedAt());
    }
}
