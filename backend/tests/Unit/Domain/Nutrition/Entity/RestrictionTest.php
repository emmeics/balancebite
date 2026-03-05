<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Nutrition\Entity;

use App\Domain\Nutrition\Entity\Restriction;
use App\Domain\Nutrition\ValueObject\RestrictionId;
use App\Domain\Nutrition\ValueObject\RestrictionSeverity;
use App\Domain\Nutrition\ValueObject\RestrictionType;
use App\Domain\User\ValueObject\HealthCondition;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Restriction Class.
 *
 * - Test the creation of the Etity Restriction with valid and invalid datas
 * - Test the methods: getters, setters
 */
final class RestrictionTest extends TestCase
{
    private string $restrictionIdString = '550e8400-e29b-41d4-a716-446655440000';
    private string $userIdString = '550e8400-e29b-41d4-a716-446655440000';
    private string $restrictionTypeString = 'intolerance';
    private string $restrictionName = 'My Gluten Restrictions';
    private string $restrictionSeverityString = 'medium';
    private string $healthConditionString = 'celiac';
    private array $foodsToAvoid = ['bread', 'pasta'];
    private array $ingredientsToCheck = ['gluten', 'wheat'];

    /**
     * Helper method to create a Restriction Object.
     *
     * @return Restriction Istance of Restriction Object
     */
    private function createRestrictionObject(): Restriction
    {
        $restrictionId = new RestrictionId($this->restrictionIdString);
        $userId = new UserId($this->userIdString);
        $restrictionType = RestrictionType::from($this->restrictionTypeString);
        $restrictionSeverity = RestrictionSeverity::from($this->restrictionSeverityString);
        $healthCondition = HealthCondition::from($this->healthConditionString);

        $restriction = new Restriction(
            $restrictionId,
            $userId,
            $restrictionType,
            $this->restrictionName,
            $restrictionSeverity,
            $healthCondition,
            $this->foodsToAvoid,
            $this->ingredientsToCheck
        );

        return $restriction;
    }

    /**
     * Test creation the object with all parameters.
     *
     * Expects:
     * - The object created exist and it's an instance of Restriction
     * - The parameters of the created object are the same as the parameters inserted
     */
    public function testCanBeCreatedWithAllPropertiesAndGettersWork(): void
    {
        $restriction = $this->createRestrictionObject();

        $this->assertTrue(
            $restriction instanceof Restriction,
            'Object $restriction should exist'
        );

        $this->assertNotEmpty($restriction->getCreatedAt(), 'Should be valorized in the creation of the object');
        $this->assertNotEmpty($restriction->getUpdatedAt(), 'Should be valorized in the creation of the object');
        $this->assertSame($this->restrictionIdString, $restriction->getId()->getValue(), 'The id should be the same');
        $this->assertSame($this->userIdString, $restriction->getUserId()->getValue(), 'The value of userId should be the same');
        $this->assertSame($this->restrictionTypeString, $restriction->getType()->value, 'The value of the restriction type should be the same');
        $this->assertSame($this->restrictionName, $restriction->getName(), 'The name should be the same');
        $this->assertSame($this->restrictionSeverityString, $restriction->getSeverity()->value, 'The value of the severity should be the same');
        $this->assertSame($this->healthConditionString, $restriction->getHealthCondition()->value, 'The value of the health condition should be the same');
        $this->assertContains($this->foodsToAvoid[0], $restriction->getFoodsToAvoid(), 'The value should be contained in the array');
        $this->assertContains($this->foodsToAvoid[1], $restriction->getFoodsToAvoid(), 'The value should be contained in the array');
        $this->assertContains($this->ingredientsToCheck[0], $restriction->getIngredientsToCheck(), 'The value should be contained in the array');
        $this->assertContains($this->ingredientsToCheck[1], $restriction->getIngredientsToCheck(), 'The value should be contained in the array');
    }

    /**
     * Test update of the object with the setter methods.
     *
     * Expects:
     * - The setter methods return the updated value
     */
    public function testCanBeUpdatedWithSetters(): void
    {
        $restriction = $this->createRestrictionObject();

        $this->assertTrue(
            $restriction instanceof Restriction,
            'Object $restriction should exist'
        );

        $dateBeforeUpdate = new \DateTimeImmutable('now');
        $dateCreatedAtBeforeUpdate = $restriction->getCreatedAt();

        $restriction->updateSeverity(RestrictionSeverity::from('low'));
        $restriction->addFoodsToAvoid(['sugar']);
        $restriction->addIngredientsToCheck(['polifenol']);

        $dateAfterUpdate = new \DateTimeImmutable();
        $dateCreatedAtAfterUpdate = $restriction->getCreatedAt();

        $this->assertSame('low', $restriction->getSeverity()->value, 'The value of the severity should be the same updated');
        $this->assertContains('sugar', $restriction->getFoodsToAvoid(), 'The value should be contained in the array');
        $this->assertContains('polifenol', $restriction->getIngredientsToCheck(), 'The value should be contained in the array');
        $this->assertSame($dateCreatedAtAfterUpdate, $dateCreatedAtBeforeUpdate, 'Should be the same value');
        $this->assertGreaterThanOrEqual(
            $dateBeforeUpdate,
            $restriction->getUpdatedAt(),
            'updatedAt should be >= time before the update'
        );
        $this->assertLessThanOrEqual(
            $dateAfterUpdate,
            $restriction->getUpdatedAt(),
            'updatedAt should be <= time after the update'
        );
    }

    /**
     * Test update of the object with the remove methods.
     *
     * Expects:
     * - The remove methods return the updated value
     */
    public function testCanBeUpdatedWithRemoveMethod(): void
    {
        $restriction = $this->createRestrictionObject();

        $this->assertTrue(
            $restriction instanceof Restriction,
            'Object $restriction should exist'
        );

        $restriction->removeFoodsToAvoid(['bread']);
        $restriction->removeIngredientsToCheck(['gluten']);

        $this->assertNotContains('bread', $restriction->getFoodsToAvoid(), 'The value should not be contained in the array');
        $this->assertNotContains('gluten', $restriction->getIngredientsToCheck(), 'The value should not be contained in the array');
    }
}
