<?php

declare(strict_types=1);

namespace App\Domain\Nutrition\Entity;

use App\Domain\Nutrition\ValueObject\RestrictionId;
use App\Domain\Nutrition\ValueObject\RestrictionSeverity;
use App\Domain\Nutrition\ValueObject\RestrictionType;
use App\Domain\User\ValueObject\HealthCondition;
use App\Domain\User\ValueObject\UserId;

/**
 * Entity representing a specific food restriction for a user.
 */
final class Restriction
{
    private RestrictionId $id;
    private UserId $userId;
    private RestrictionType $type;
    private string $name;
    private RestrictionSeverity $severity;
    private HealthCondition $healthCondition;

    /**
     * @var array<string>
     */
    private array $foodsToAvoid;

    /**
     * @var array<string>
     */
    private array $ingredientsToCheck;

    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /**
     * @param array<string> $foodsToAvoid
     * @param array<string> $ingredientsToCheck
     */
    public function __construct(
        RestrictionId $id,
        UserId $userId,
        RestrictionType $type,
        string $name,
        RestrictionSeverity $severity,
        HealthCondition $healthCondition,
        array $foodsToAvoid,
        array $ingredientsToCheck,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->type = $type;
        $this->name = $name;
        $this->severity = $severity;
        $this->healthCondition = $healthCondition;
        $this->foodsToAvoid = $foodsToAvoid;
        $this->ingredientsToCheck = $ingredientsToCheck;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Return the Id of The Restriction.
     */
    public function getId(): RestrictionId
    {
        return $this->id;
    }

    /**
     * Return the UserId of User associated.
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * Return the Type of the Restriction.
     */
    public function getType(): RestrictionType
    {
        return $this->type;
    }

    /**
     * Return the name of the Restriction.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the Severity of the Restriction.
     */
    public function getSeverity(): RestrictionSeverity
    {
        return $this->severity;
    }

    /**
     * Return the HealthCondition of the Restriction.
     */
    public function getHealthCondition(): HealthCondition
    {
        return $this->healthCondition;
    }

    /**
     * Return a list of the foods to avoid.
     *
     * @return string[]
     */
    public function getFoodsToAvoid(): array
    {
        return $this->foodsToAvoid;
    }

    /**
     * Return a list of ingredients to check.
     *
     * @return string[]
     */
    public function getIngredientsToCheck(): array
    {
        return $this->ingredientsToCheck;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Method to update the Severity of Restriction.
     */
    public function updateSeverity(RestrictionSeverity $restrictionSeverity): void
    {
        $this->severity = $restrictionSeverity;
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    /**
     * Method to add new Foods to foodsToAvoid list.
     *
     * @param array<string> $foodsToAvoid
     */
    public function addFoodsToAvoid(array $foodsToAvoid): void
    {
        foreach ($foodsToAvoid as $foodToAvoid) {
            if (!in_array($foodToAvoid, $this->foodsToAvoid)) {
                $this->foodsToAvoid[] = $foodToAvoid;
            }
        }
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    /**
     * Method to add new Ingredients to ingredientsToCheck list.
     *
     * @param array<string> $ingredientsToCheck
     */
    public function addIngredientsToCheck(array $ingredientsToCheck): void
    {
        foreach ($ingredientsToCheck as $ingredientToCheck) {
            if (!in_array($ingredientToCheck, $this->ingredientsToCheck)) {
                $this->ingredientsToCheck[] = $ingredientToCheck;
            }
        }
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    /**
     * Method to remove Foods from foodsToAvoid list.
     *
     * @param array<string> $foodsToAvoid
     */
    public function removeFoodsToAvoid(array $foodsToAvoid): void
    {
        foreach ($foodsToAvoid as $foodToAvoid) {
            $key = array_search($foodToAvoid, $this->foodsToAvoid);
            if (false !== $key) {
                unset($this->foodsToAvoid[$key]);
            }
        }
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    /**
     * Method to remove Ingredients from ingredientsToCheck list.
     *
     * @param array<string> $ingredientsToCheck
     */
    public function removeIngredientsToCheck(array $ingredientsToCheck): void
    {
        foreach ($ingredientsToCheck as $ingredientToCheck) {
            $key = array_search($ingredientToCheck, $this->ingredientsToCheck);
            if (false !== $key) {
                unset($this->ingredientsToCheck[$key]);
            }
        }
        $this->updatedAt = new \DateTimeImmutable('now');
    }
}
