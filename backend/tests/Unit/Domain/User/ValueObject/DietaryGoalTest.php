<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\DietaryGoal;
use PHPUnit\Framework\TestCase;

class DietaryGoalTest extends TestCase
{
    public function testWeightLossCase(): void
    {
        $this->assertSame('weight_loss', DietaryGoal::WEIGHT_LOSS->value);
    }

    public function testMaintainCase(): void
    {
        $this->assertSame('maintain', DietaryGoal::MAINTAIN->value);
    }

    public function testWeightGainCase(): void
    {
        $this->assertSame('weight_gain', DietaryGoal::WEIGHT_GAIN->value);
    }

    public function testMuscleBuildingCase(): void
    {
        $this->assertSame('muscle_building', DietaryGoal::MUSCLE_BUILDING->value);
    }

    public function testCanCreateFromString(): void
    {
        $this->assertSame(DietaryGoal::from('maintain')->value, DietaryGoal::MAINTAIN->value);
    }
}
