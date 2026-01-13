<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\HealthCondition;
use PHPUnit\Framework\TestCase;

class HealthConditionTest extends TestCase
{
    public function testCeliacCase(): void
    {
        $this->assertSame('celiac', HealthCondition::CELIAC->value);
    }

    public function testLactoseIntoleranceCase(): void
    {
        $this->assertSame('lactose_intolerance', HealthCondition::LACTOSE_INTOLERANCE->value);
    }

    public function testIbsCase(): void
    {
        $this->assertSame('ibs', HealthCondition::IBS->value);
    }

    public function testRefluxCase(): void
    {
        $this->assertSame('reflux', HealthCondition::REFLUX->value);
    }

    public function testDiabetesCase(): void
    {
        $this->assertSame('diabetes', HealthCondition::DIABETES->value);
    }

    public function testHypertensionCase(): void
    {
        $this->assertSame('hypertension', HealthCondition::HYPERTENSION->value);
    }

    public function testCanCreateFromString(): void
    {
        $this->assertSame(HealthCondition::from('ibs')->value, HealthCondition::IBS->value);
    }
}
