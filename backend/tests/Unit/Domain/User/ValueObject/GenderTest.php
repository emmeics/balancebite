<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\User\ValueObject\Gender;

class GenderTest extends TestCase
{
    public function testMaleCase(): void
    {
        $this->assertSame('male', Gender::MALE->value);
    }

    public function testFemaleCase(): void
    {
        $this->assertSame('female', Gender::FEMALE->value);
    }

    public function testOtherCase(): void
    {
        $this->assertSame('other', Gender::OTHER->value);
    }

    public function testCanCreateFromString(): void
    {
        $this->assertSame(Gender::from('other')->value, Gender::OTHER->value);
    }
}