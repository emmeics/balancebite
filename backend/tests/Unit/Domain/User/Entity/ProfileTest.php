<?php

namespace App\Tests\Unit\Domain\User\Entity;

use App\Domain\User\Entity\Profile;
use App\Domain\User\ValueObject\ActivityLevel;
use App\Domain\User\ValueObject\DietaryGoal;
use App\Domain\User\ValueObject\Gender;
use App\Domain\User\ValueObject\HealthCondition;
use App\Domain\User\ValueObject\ProfileId;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    public function testCanCreateProfile(): void
    {
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $firstName = 'Mario';
        $lastName = 'Rossi';
        $birthDay = new \DateTimeImmutable('now');
        $heightCm = 180;
        $weightKg = 80.0;

        $profile = Profile::create(
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            Gender::from('male'),
            $heightCm,
            $weightKg,
            ActivityLevel::from('sedentary'),
            DietaryGoal::from('maintain')
        );

        $this->assertSame($userId->getValue(), $profile->getUserId()->getValue());
        $this->assertSame($firstName, $profile->getFirstName());
        $this->assertSame($lastName, $profile->getLastName());
        $this->assertSame($birthDay, $profile->getBirthDay());
        $this->assertSame(Gender::MALE->value, $profile->getGender()->value);
        $this->assertSame($heightCm, $profile->getHeight());
        $this->assertSame($weightKg, $profile->getWeight());
        $this->assertSame(ActivityLevel::SEDENTARY->value, $profile->getActivityLevel()->value);
        $this->assertSame(DietaryGoal::MAINTAIN->value, $profile->getDietaryGoal()->value);
    }

    public function testCanReconstituteProfileAndGetters(): void
    {
        $profileId = new ProfileId('560e8400-e29b-41d4-a716-446655440000');
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $firstName = 'Mario';
        $lastName = 'Rossi';
        $birthDay = new \DateTimeImmutable('now');
        $heightCm = 180;
        $weightKg = 80.0;
        $healthConditions = [HealthCondition::from('ibs'), HealthCondition::from('reflux')];
        $updatedAt = new \DateTimeImmutable('now');

        $profile = Profile::reconstitute(
            $profileId,
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            Gender::from('male'),
            $heightCm,
            $weightKg,
            ActivityLevel::from('sedentary'),
            DietaryGoal::from('maintain'),
            $healthConditions,
            $updatedAt
        );

        $this->assertSame($userId->getValue(), $profile->getUserId()->getValue());
        $this->assertSame($profileId->getValue(), $profile->getProfileId()->getValue());
        $this->assertSame($firstName, $profile->getFirstName());
        $this->assertSame($lastName, $profile->getLastName());
        $this->assertSame($birthDay, $profile->getBirthDay());
        $this->assertSame(Gender::MALE->value, $profile->getGender()->value);
        $this->assertSame($heightCm, $profile->getHeight());
        $this->assertSame($weightKg, $profile->getWeight());
        $this->assertSame(ActivityLevel::SEDENTARY->value, $profile->getActivityLevel()->value);
        $this->assertSame(DietaryGoal::MAINTAIN->value, $profile->getDietaryGoal()->value);
        $this->assertContains(HealthCondition::IBS, $profile->getHealthConditions());
        $this->assertSame($updatedAt, $profile->getUpdatedAt());
    }

    public function testCanAddHealthConditionsOnProfile(): void
    {
        $newHealthCondition = HealthCondition::from('ibs');

        $profileId = new ProfileId('560e8400-e29b-41d4-a716-446655440000');
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $firstName = 'Mario';
        $lastName = 'Rossi';
        $birthDay = new \DateTimeImmutable('now');
        $heightCm = 180;
        $weightKg = 80.0;
        $healthConditions = [HealthCondition::from('reflux')];
        $updatedAt = new \DateTimeImmutable('now');

        $profile = Profile::reconstitute(
            $profileId,
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            Gender::from('male'),
            $heightCm,
            $weightKg,
            ActivityLevel::from('sedentary'),
            DietaryGoal::from('maintain'),
            $healthConditions,
            $updatedAt
        );

        $originalUpdatedAt = $profile->getUpdatedAt();
        $profile->addHealthCondition($newHealthCondition);

        $this->assertContains($newHealthCondition, $profile->getHealthConditions());
        $this->assertNotSame($originalUpdatedAt, $profile->getUpdatedAt());
    }

    public function testCanRemoveHealthConditionsOnProfile(): void
    {
        $delHealthCondition = HealthCondition::from('ibs');

        $profileId = new ProfileId('560e8400-e29b-41d4-a716-446655440000');
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $firstName = 'Mario';
        $lastName = 'Rossi';
        $birthDay = new \DateTimeImmutable('now');
        $heightCm = 180;
        $weightKg = 80.0;
        $healthConditions = [HealthCondition::from('ibs'), HealthCondition::from('reflux')];
        $updatedAt = new \DateTimeImmutable('now');

        $profile = Profile::reconstitute(
            $profileId,
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            Gender::from('male'),
            $heightCm,
            $weightKg,
            ActivityLevel::from('sedentary'),
            DietaryGoal::from('maintain'),
            $healthConditions,
            $updatedAt
        );

        $originalUpdatedAt = $profile->getUpdatedAt();
        $profile->removeHealthCondition($delHealthCondition);

        $this->assertNotContains($delHealthCondition, $profile->getHealthConditions());
        $this->assertNotSame($originalUpdatedAt, $profile->getUpdatedAt());
    }

    public function testBasicInfoAreUpdated(): void
    {
        $profileId = new ProfileId('560e8400-e29b-41d4-a716-446655440000');
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $firstName = 'Mario';
        $lastName = 'Rossi';
        $birthDay = new \DateTimeImmutable('now');
        $heightCm = 180;
        $weightKg = 80.0;
        $healthConditions = [HealthCondition::from('ibs'), HealthCondition::from('reflux')];
        $updatedAt = new \DateTimeImmutable('now');

        $profile = Profile::reconstitute(
            $profileId,
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            Gender::from('male'),
            $heightCm,
            $weightKg,
            ActivityLevel::from('sedentary'),
            DietaryGoal::from('maintain'),
            $healthConditions,
            $updatedAt
        );

        $originalUpdatedAt = $profile->getUpdatedAt();
        $newBirthDay = new \DateTimeImmutable('now');

        $profile->updateBasicInfo(
            'Luigi',
            'Bianchi',
            $newBirthDay,
            Gender::from('other'),
            185,
            90.0,
            ActivityLevel::from('active'),
            DietaryGoal::from('weight_loss')
        );

        $this->assertSame('Luigi', $profile->getFirstName());
        $this->assertSame('Bianchi', $profile->getLastName());
        $this->assertSame($newBirthDay, $profile->getBirthDay());
        $this->assertSame(Gender::OTHER->value, $profile->getGender()->value);
        $this->assertSame(185, $profile->getHeight());
        $this->assertSame(90.0, $profile->getWeight());
        $this->assertSame(ActivityLevel::ACTIVE->value, $profile->getActivityLevel()->value);
        $this->assertSame(DietaryGoal::WEIGHT_LOSS->value, $profile->getDietaryGoal()->value);

        $this->assertNotSame($originalUpdatedAt, $profile->getUpdatedAt());
    }

    public function testSomeBasicInfoAreUpdated(): void
    {
        $profileId = new ProfileId('560e8400-e29b-41d4-a716-446655440000');
        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $firstName = 'Mario';
        $lastName = 'Rossi';
        $birthDay = new \DateTimeImmutable('now');
        $heightCm = 180;
        $weightKg = 80.0;
        $healthConditions = [HealthCondition::from('ibs'), HealthCondition::from('reflux')];
        $updatedAt = new \DateTimeImmutable('now');

        $profile = Profile::reconstitute(
            $profileId,
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            Gender::from('male'),
            $heightCm,
            $weightKg,
            ActivityLevel::from('sedentary'),
            DietaryGoal::from('maintain'),
            $healthConditions,
            $updatedAt
        );

        $originalUpdatedAt = $profile->getUpdatedAt();
        $newBirthDay = new \DateTimeImmutable('now');

        $profile->updateBasicInfo(
            null,
            null,
            $newBirthDay,
            null,
            null,
            70.0,
            ActivityLevel::from('active'),
            DietaryGoal::from('weight_loss')
        );

        $this->assertSame($firstName, $profile->getFirstName());
        $this->assertSame($lastName, $profile->getLastName());
        $this->assertSame($newBirthDay, $profile->getBirthDay());
        $this->assertSame(Gender::MALE->value, $profile->getGender()->value);
        $this->assertSame($heightCm, $profile->getHeight());
        $this->assertSame(70.0, $profile->getWeight());
        $this->assertSame(ActivityLevel::ACTIVE->value, $profile->getActivityLevel()->value);
        $this->assertSame(DietaryGoal::WEIGHT_LOSS->value, $profile->getDietaryGoal()->value);

        $this->assertNotSame($originalUpdatedAt, $profile->getUpdatedAt());
    }
}
