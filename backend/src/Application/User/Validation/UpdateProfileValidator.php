<?php

namespace App\Application\User\Validation;

use App\Application\Exception\ValidationException;
use App\Application\User\Command\UpdateProfileCommand;
use App\Domain\User\ValueObject\ActivityLevel;
use App\Domain\User\ValueObject\DietaryGoal;
use App\Domain\User\ValueObject\Gender;
use App\Domain\User\ValueObject\HealthCondition;

/**
 * Validator for UpdateProfileCommand.
 *
 * Validates all profile fields and throws ValidationException
 * if any validation fails.
 */
class UpdateProfileValidator
{
    /**
     * Validate the profile command data.
     *
     * @param UpdateProfileCommand $command      The command to validate
     * @param bool                 $isNewProfile Whether this is a new profile (mandatory fields required)
     *
     * @throws ValidationException If validation fails
     */
    public function validate(
        UpdateProfileCommand $command,
        bool $isNewProfile,
    ): void {
        $errors = [];

        if ($isNewProfile && empty($command->firstName)) {
            $errors['first_name'] = 'First name is required';
        }

        if ($isNewProfile && empty($command->lastName)) {
            $errors['last_name'] = 'Last name is required';
        }

        if ($isNewProfile || !empty($command->birthDate)) {
            $date = \DateTimeImmutable::createFromFormat('Y-m-d', $command->birthDate);
            if (false === $date) {
                $errors['birth_date'] = 'Invalid date format. Expected: Y-m-d';
            }
        }

        if ($isNewProfile || !empty($command->gender)) {
            if (null === Gender::tryFrom($command->gender)) {
                $errors['gender'] = 'Gender is not valid';
            }
        }

        if ($isNewProfile && (int) $command->heightCm <= 0) {
            $errors['height_cm'] = 'Height Cm is required';
        }

        if ($isNewProfile && (float) $command->weightKg <= 0) {
            $errors['weight_kg'] = 'Weight Kg is required';
        }

        if ($isNewProfile || !empty($command->activityLevel)) {
            if (null === ActivityLevel::tryFrom($command->activityLevel)) {
                $errors['activity_level'] = 'Activity Level is invalid';
            }
        }

        if ($isNewProfile || !empty($command->dietaryGoal)) {
            if (null === DietaryGoal::tryFrom($command->dietaryGoal)) {
                $errors['dietary_goal'] = 'Dietary Goal is invalid';
            }
        }

        if (!empty($command->healthConditions)) {
            $healthConditions = [];
            foreach ($command->healthConditions as $condition) {
                if (null === HealthCondition::tryFrom($condition)) {
                    $healthConditions[] = $condition;
                }
            }
            if (!empty($healthConditions)) {
                $errors['health_conditions'] = 'One or more Health Condition are invalid: '.implode(', ', $healthConditions);
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
