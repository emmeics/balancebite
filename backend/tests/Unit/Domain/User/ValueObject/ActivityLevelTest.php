<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\ActivityLevel;
use PHPUnit\Framework\TestCase;

class ActivityLevelTest extends TestCase
{
    public function testSedentaryCase(): void
    {
        $this->assertSame('sedentary', ActivityLevel::SEDENTARY->value);
    }

    public function testLightCase(): void
    {
        $this->assertSame('light', ActivityLevel::LIGHT->value);
    }

    public function testModerateCase(): void
    {
        $this->assertSame('moderate', ActivityLevel::MODERATE->value);
    }

    public function testActiveCase(): void
    {
        $this->assertSame('active', ActivityLevel::ACTIVE->value);
    }

    public function testVeryActiveCase(): void
    {
        $this->assertSame('very_active', ActivityLevel::VERY_ACTIVE->value);
    }

    public function testCanCreateFromString(): void
    {
        $this->assertSame(ActivityLevel::from('light')->value, ActivityLevel::LIGHT->value);
    }
}
