<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidPasswordException;
use App\Domain\User\ValueObject\HashedPassword;
use PHPUnit\Framework\TestCase;

class HashedPasswordTest extends TestCase
{
    public function testCanBeCreatedWithValidHashAndGetValue(): void
    {
        $hashedPasswd = 'HashedPassword';

        $passwd = new HashedPassword($hashedPasswd);

        $this->assertSame($hashedPasswd, $passwd->getValue());
    }

    public function testCannotBeCreatedWithEmptyHash(): void
    {
        $hashedPasswd = '';

        $this->expectException(InvalidPasswordException::class);

        $passwd = new HashedPassword($hashedPasswd);
    }
}
