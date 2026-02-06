<?php

namespace App\Application\User\Command;

/**
 * Command to update user profile data.
 *
 * Carries all profile information from the controller to the handler.
 */
final class UpdateProfileCommand
{
    /**
     * @param string        $userId           The authenticated user's ID
     * @param string        $firstName        User's first name
     * @param string        $lastName         User's last name
     * @param string        $birthDate        Birth date in Y-m-d format
     * @param string        $gender           Gender enum value (male, female, other)
     * @param int           $heightCm         Height in centimeters
     * @param float         $weightKg         Weight in kilograms
     * @param string        $activityLevel    Activity level enum value
     * @param string        $dietaryGoal      Dietary goal enum value
     * @param array<string> $healthConditions Array of health condition enum values
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $birthDate,
        public readonly string $gender,
        public readonly int $heightCm,
        public readonly float $weightKg,
        public readonly string $activityLevel,
        public readonly string $dietaryGoal,
        /** @var array<string> */
        public readonly array $healthConditions,
    ) {
    }
}
