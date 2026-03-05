<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Nutrition\Entity;

use App\Domain\Nutrition\Entity\RestrictionRule;
use App\Domain\Nutrition\ValueObject\RestrictionRuleId;
use App\Domain\Nutrition\ValueObject\RestrictionSeverity;
use App\Domain\Nutrition\ValueObject\RestrictionType;
use App\Domain\User\ValueObject\HealthCondition;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for RestrictionRule Class.
 *
 * - Test the creation of the Etity RestrictionRule with valid and invalid datas
 * - Test the methods: getters, setters
 */
class RestrictionRuleTest extends TestCase
{
    private string $restrictionRuleIdString = '550e8400-e29b-41d4-a716-446655440000';
    private string $restrictionRuleTypeString = 'intolerance';
    private string $restrictionRuleName = 'Medium Gluten Restrictions';
    private string $restrictionRuleSeverityString = 'medium';
    private string $healthConditionString = 'celiac';
    private array $foodsToAvoid = ['bread', 'pasta'];
    private array $ingredientsToCheck = ['gluten', 'wheat'];

    /**
     * Helper method to create a RestrictionRule Object.
     *
     * @return RestrictionRule Istance of RestrictionRule Object
     */
    private function createRestrictionRuleObject(): RestrictionRule
    {
        $id = new RestrictionRuleId($this->restrictionRuleIdString);
        $type = RestrictionType::from($this->restrictionRuleTypeString);
        $severity = RestrictionSeverity::from($this->restrictionRuleSeverityString);
        $condition = HealthCondition::from($this->healthConditionString);

        $restrictionRule = new RestrictionRule(
            $id,
            $this->restrictionRuleName,
            $type,
            $condition,
            $severity,
            $this->foodsToAvoid,
            $this->ingredientsToCheck
        );

        return $restrictionRule;
    }

    /**
     * Test creation the object with all parameters.
     *
     * Expects:
     * - The object created exists, and it's an instance of RestrictionRule
     * - The parameters of the created object are the same as the parameters inserted
     */
    public function testCanBeCreatedWithAllPropertiesAndGettersWork(): void
    {
        $restrictionRule = $this->createRestrictionRuleObject();

        $this->assertTrue(
            $restrictionRule instanceof RestrictionRule,
            'Object $restrictionRule should exist'
        );

        $this->assertNotEmpty($restrictionRule->getCreatedAt(), 'Should be valorized in the creation of the object');
        $this->assertNotEmpty($restrictionRule->getUpdatedAt(), 'Should be valorized in the creation of the object');
        $this->assertSame($this->restrictionRuleIdString, $restrictionRule->getId()->getValue(), 'The id should be the same');
        $this->assertSame($this->restrictionRuleTypeString, $restrictionRule->getType()->value, 'The value of the restriction type should be the same');
        $this->assertSame($this->restrictionRuleName, $restrictionRule->getName(), 'The name should be the same');
        $this->assertSame($this->restrictionRuleSeverityString, $restrictionRule->getSeverity()->value, 'The value of the severity should be the same');
        $this->assertSame($this->healthConditionString, $restrictionRule->getHealthCondition()->value, 'The value of the health condition should be the same');
        $this->assertContains($this->foodsToAvoid[0], $restrictionRule->getFoodsToAvoid(), 'The value should be contained in the array');
        $this->assertContains($this->foodsToAvoid[1], $restrictionRule->getFoodsToAvoid(), 'The value should be contained in the array');
        $this->assertContains($this->ingredientsToCheck[0], $restrictionRule->getIngredientsToCheck(), 'The value should be contained in the array');
        $this->assertContains($this->ingredientsToCheck[1], $restrictionRule->getIngredientsToCheck(), 'The value should be contained in the array');
    }

    /**
     * Test update of the object with the setter methods.
     *
     * Expects:
     * - The setter methods return the updated value
     */
    public function testCanBeUpdatedWithSetters(): void
    {
        $restrictionRule = $this->createRestrictionRuleObject();

        $this->assertTrue(
            $restrictionRule instanceof RestrictionRule,
            'Object $restriction should exist'
        );

        $dateBeforeUpdate = new \DateTimeImmutable('now');
        $dateCreatedAtBeforeUpdate = $restrictionRule->getCreatedAt();

        $restrictionRule->updateSeverity(RestrictionSeverity::from('low'));
        $restrictionRule->addFoodsToAvoid(['sugar']);
        $restrictionRule->addIngredientsToCheck(['polifenol']);

        $dateAfterUpdate = new \DateTimeImmutable();
        $dateCreatedAtAfterUpdate = $restrictionRule->getCreatedAt();

        $this->assertSame('low', $restrictionRule->getSeverity()->value, 'The value of the severity should be the same updated');
        $this->assertContains('sugar', $restrictionRule->getFoodsToAvoid(), 'The value should be contained in the array');
        $this->assertContains('polifenol', $restrictionRule->getIngredientsToCheck(), 'The value should be contained in the array');
        $this->assertSame($dateCreatedAtAfterUpdate, $dateCreatedAtBeforeUpdate, 'Should be the same value');
        $this->assertGreaterThanOrEqual(
            $dateBeforeUpdate,
            $restrictionRule->getUpdatedAt(),
            'updatedAt should be >= time before the update'
        );
        $this->assertLessThanOrEqual(
            $dateAfterUpdate,
            $restrictionRule->getUpdatedAt(),
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
        $restrictionRule = $this->createRestrictionRuleObject();

        $this->assertTrue(
            $restrictionRule instanceof RestrictionRule,
            'Object $restriction should exist'
        );

        $restrictionRule->removeFoodsToAvoid(['bread']);
        $restrictionRule->removeIngredientsToCheck(['gluten']);

        $this->assertNotContains('bread', $restrictionRule->getFoodsToAvoid(), 'The value should not be contained in the array');
        $this->assertNotContains('gluten', $restrictionRule->getIngredientsToCheck(), 'The value should not be contained in the array');
    }
}
