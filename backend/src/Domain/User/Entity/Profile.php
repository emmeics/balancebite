<?php

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\ActivityLevel;
use App\Domain\User\ValueObject\DietaryGoal;
use App\Domain\User\ValueObject\Gender;
use App\Domain\User\ValueObject\HealthCondition;
use App\Domain\User\ValueObject\ProfileId;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'profiles')]
final class Profile
{
    #[ORM\Id]
    #[ORM\Column(type: 'profile_id', length: 36)]
    private ProfileId $id;

    #[ORM\Column(type: 'user_id', length: 36)]
    private UserId $userId;

    #[ORM\Column(type: 'string')]
    private string $firstName;

    #[ORM\Column(type: 'string')]
    private string $lastName;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $birthDay;

    #[ORM\Column(type: 'string', enumType: Gender::class)]
    private Gender $gender;

    #[ORM\Column(type: 'integer')]
    private int $heightCm;

    #[ORM\Column(type: 'float')]
    private float $weightKg;

    #[ORM\Column(type: 'string', enumType: ActivityLevel::class)]
    private ActivityLevel $activityLevel;

    #[ORM\Column(type: 'string', enumType: DietaryGoal::class)]
    private DietaryGoal $dietaryGoal;

    #[ORM\Column(type: 'health_conditions')]
    private array $healthConditions = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        ProfileId $profileId,
        UserId $userId,
        string $firstName,
        string $lastName,
        \DateTimeImmutable $birthDay,
        Gender $gender,
        int $heightCm,
        float $weightKg,
        ActivityLevel $activityLevel,
        DietaryGoal $dietaryGoal,
        array $healthConditions,
        \DateTimeImmutable $updatedAt,
    ) {
        $this->id = $profileId;
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthDay = $birthDay;
        $this->gender = $gender;
        $this->heightCm = $heightCm;
        $this->weightKg = $weightKg;
        $this->activityLevel = $activityLevel;
        $this->dietaryGoal = $dietaryGoal;
        $this->healthConditions = $healthConditions;
        $this->updatedAt = $updatedAt;
    }

    public function getProfileId(): ProfileId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getBirthDay(): \DateTimeImmutable
    {
        return $this->birthDay;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function getHeight(): int
    {
        return $this->heightCm;
    }

    public function getWeight(): float
    {
        return $this->weightKg;
    }

    public function getActivityLevel(): ActivityLevel
    {
        return $this->activityLevel;
    }

    public function getDietaryGoal(): DietaryGoal
    {
        return $this->dietaryGoal;
    }

    public function getHealthConditions(): array
    {
        return $this->healthConditions;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function create(
        UserId $userId,
        string $firstName,
        string $lastName,
        \DateTimeImmutable $birthDay,
        Gender $gender,
        int $heightCm,
        float $weightKg,
        ActivityLevel $activityLevel,
        DietaryGoal $dietaryGoal,
    ): self {
        $profileId = ProfileId::generate();

        return new self(
            $profileId,
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            $gender,
            $heightCm,
            $weightKg,
            $activityLevel,
            $dietaryGoal,
            [],
            new \DateTimeImmutable('now')
        );
    }

    public static function reconstitute(
        ProfileId $profileId,
        UserId $userId,
        string $firstName,
        string $lastName,
        \DateTimeImmutable $birthDay,
        Gender $gender,
        int $heightCm,
        float $weightKg,
        ActivityLevel $activityLevel,
        DietaryGoal $dietaryGoal,
        array $healthConditions,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $profileId,
            $userId,
            $firstName,
            $lastName,
            $birthDay,
            $gender,
            $heightCm,
            $weightKg,
            $activityLevel,
            $dietaryGoal,
            $healthConditions,
            $updatedAt
        );
    }

    public function addHealthCondition(HealthCondition $healthCondition): bool
    {
        if (!in_array($healthCondition, $this->healthConditions)) {
            $this->healthConditions[] = $healthCondition;
            $this->updatedAt = new \DateTimeImmutable('now');

            return true;
        }

        return false;
    }

    public function removeHealthCondition(HealthCondition $healthCondition): void
    {
        if (($key = array_search($healthCondition, $this->healthConditions, true)) !== false) {
            unset($this->healthConditions[$key]);
            $this->updatedAt = new \DateTimeImmutable('now');
        }
    }

    public function updateBasicInfo(
        ?string $firstName = null,
        ?string $lastName = null,
        ?\DateTimeImmutable $birthDay = null,
        ?Gender $gender = null,
        ?int $heightCm = null,
        ?float $weightKg = null,
        ?ActivityLevel $activityLevel = null,
        ?DietaryGoal $dietaryGoal = null,
    ): void {
        $entityUpdated = false;
        if (!is_null($firstName) || !is_null($lastName) || !is_null($birthDay) || !is_null($gender)
            || !is_null($heightCm) || !is_null($weightKg) || !is_null($activityLevel) || !is_null($dietaryGoal)) {
            $entityUpdated = true;
        }

        $this->firstName = $firstName ?? $this->firstName;
        $this->lastName = $lastName ?? $this->lastName;
        $this->birthDay = $birthDay ?? $this->birthDay;
        $this->gender = $gender ?? $this->gender;
        $this->heightCm = $heightCm ?? $this->heightCm;
        $this->weightKg = $weightKg ?? $this->weightKg;
        $this->activityLevel = $activityLevel ?? $this->activityLevel;
        $this->dietaryGoal = $dietaryGoal ?? $this->dietaryGoal;

        if ($entityUpdated) {
            $this->updatedAt = new \DateTimeImmutable('now');
        }
    }
}
