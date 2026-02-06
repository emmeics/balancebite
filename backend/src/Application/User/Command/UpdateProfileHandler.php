<?php

namespace App\Application\User\Command;

use App\Application\User\Validation\UpdateProfileValidator;
use App\Domain\User\Entity\Profile;
use App\Domain\User\Repository\ProfileRepositoryInterface;
use App\Domain\User\ValueObject\ActivityLevel;
use App\Domain\User\ValueObject\DietaryGoal;
use App\Domain\User\ValueObject\Gender;
use App\Domain\User\ValueObject\HealthCondition;
use App\Domain\User\ValueObject\UserId;

/**
 * Handler to update create new user profile.
 *
 * Carries all profile information from the controller to the handler.
 */
final class UpdateProfileHandler
{
    public function __construct(
        private ProfileRepositoryInterface $profileRepository,
        private UpdateProfileValidator $profileValidator,
    ) {
    }

    /**
     * Handle the Profile user or update.
     *
     * Flow:
     * - Verify if Profile just exist by UserId
     * - Create or Update the Profile
     *
     * @param UpdateProfileCommand $command The command with all profile data
     *
     * @return Profile The created or updated profile
     */
    public function handle(UpdateProfileCommand $command): Profile
    {
        $userId = new UserId($command->userId);

        $profile = null;
        $found = $this->profileRepository->findByUserId($userId);

        // Validate all fields
        $this->profileValidator->validate($command, null === $found);

        if ($found) {
            $profile = $found;

            // Update Profile
            $profile->updateBasicInfo(
                $command->firstName,
                $command->lastName,
                new \DateTimeImmutable($command->birthDate),
                $command->gender ? Gender::from($command->gender) : null,
                $command->heightCm,
                $command->weightKg,
                $command->activityLevel ? ActivityLevel::from($command->activityLevel) : null,
                $command->dietaryGoal ? DietaryGoal::from($command->dietaryGoal) : null
            );

            if (!empty($command->healthConditions)) {
                $newConditions = array_map(
                    fn (string $c) => HealthCondition::from($c),
                    $command->healthConditions
                );

                foreach ($profile->getHealthConditions() as $existingCondition) {
                    if (!in_array($existingCondition, $newConditions, true)) {
                        $profile->removeHealthCondition($existingCondition);
                    }
                }
            }
        } else {
            $profile = Profile::create(
                $userId,
                $command->firstName,
                $command->lastName,
                new \DateTimeImmutable($command->birthDate),
                Gender::from($command->gender),
                $command->heightCm,
                $command->weightKg,
                ActivityLevel::from($command->activityLevel),
                DietaryGoal::from($command->dietaryGoal)
            );
        }

        if (!empty($command->healthConditions)) {
            foreach ($command->healthConditions as $healthCondition) {
                $profile->addHealthCondition(HealthCondition::from($healthCondition));
            }
        }

        $this->profileRepository->save($profile);

        return $profile;
    }
}
