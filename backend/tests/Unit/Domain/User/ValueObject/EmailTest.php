<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidEmailException;
use App\Domain\User\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testCanBeCreatedWithValidEmailAndGetValue(): void
    {
        $validMail = 'test@mail.com';

        $email = new Email($validMail);

        $this->assertSame($validMail, $email->getValue());
    }

    public function testCannotBeCreatedWithInvalidEmail(): void
    {
        $this->expectException(InvalidEmailException::class);

        $invalidMail = 'test@mail@com';

        $email = new Email($invalidMail);
    }

    public function testGetDomain(): void
    {
        $validMail = 'test@mail.com';
        $expectedDomain = 'mail.com';

        $email = new Email($validMail);

        $this->assertSame($expectedDomain, $email->getDomain());
    }

    public function testEqualsTrueForSameValue(): void
    {
        $validMail = 'test@mail.com';

        $email1 = new Email($validMail);
        $email2 = new Email($validMail);

        $this->assertTrue($email1->equals($email2));
    }

    public function testEqualsFalseForDifferentValue(): void
    {
        $email1 = new Email('test@mail.com');
        $email2 = new Email('testtwo@gmail.com');

        $this->assertFalse($email1->equals($email2));
    }
}
