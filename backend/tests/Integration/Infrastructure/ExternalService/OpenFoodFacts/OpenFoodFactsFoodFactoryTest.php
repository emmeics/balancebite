<?php

namespace App\Tests\Integration\Infrastructure\ExternalService\OpenFoodFacts;

use App\Domain\Nutrition\Entity\Food;
use App\Domain\Nutrition\Exception\InvalidFoodDataException;
use App\Domain\Nutrition\ValueObject\FoodSource;
use App\Domain\Nutrition\ValueObject\NutrientInfo;
use App\Infrastructure\ExternalService\OpenFoodFacts\OpenFoodFactsClient;
use App\Infrastructure\ExternalService\OpenFoodFacts\OpenFoodFactsFoodFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Integration tests for OpenFoodFacts Food Factory Class.
 *
 * Tests the process of reading data from OpenFoodFacts API and transform it to Entity Objects.
 */
class OpenFoodFactsFoodFactoryTest extends KernelTestCase
{
    private OpenFoodFactsClient $openFoodFactsClient;
    private OpenFoodFactsFoodFactory $openFoodFactsFactory;

    /**
     * Set up test environment before each test.
     *
     * Initializes:
     * - OpenFoodFactsClient: to read informations from datasource
     * - OpenFoodFactsFactory: the class to test
     */
    protected function setUp(): void
    {
        $container = self::getContainer();
        $httpClient = $container->get(HttpClientInterface::class);
        $this->openFoodFactsClient = new OpenFoodFactsClient($httpClient);
        $this->openFoodFactsFactory = new OpenFoodFactsFoodFactory();
        parent::setUp();
    }

    /**
     * Tests the correct work of method "createFromArray" with getById format.
     *
     * Expects:
     * - Return a valid Food Entity
     * - Return a Food Entity with correct data
     */
    public function testCreateFromArrayWithGetByIdFormat(): void
    {
        $productId = '3017620422003';
        $data = $this->openFoodFactsClient->getById($productId);
        $foodEntity = $this->openFoodFactsFactory->createFromArray($data);

        $this->assertInstanceOf(Food::class, $foodEntity, 'Should return a Food Entity.');
        $this->assertInstanceOf(NutrientInfo::class, $foodEntity->getNutrients(), 'Should return a NutrientInfo Entity');
        $this->assertSame($productId, $foodEntity->getId()->getValue(), 'Should have the same FoodId value');
        $this->assertNotEmpty($foodEntity->getName(), 'Should be not empty');
        $this->assertSame(FoodSource::OPEN_FOOD_FACTS->value, $foodEntity->getSource()->value, 'Should be the same Source');
    }

    /**
     * Tests the correct work of method "createFromArray" with search format.
     *
     * Expects:
     * - Return a valid Food Entity
     * - Return a Food Entity with correct data
     */
    public function testCreateFromArrayWithSearchFormat(): void
    {
        $search = 'nutella';
        $data = $this->openFoodFactsClient->search($search, 1);

        if (empty($data['products'][0])) {
            $this->markTestSkipped('OpenFoodFacts API returned empty response.');
        }

        $foodEntity = $this->openFoodFactsFactory->createFromArray($data['products'][0]);

        $this->assertInstanceOf(Food::class, $foodEntity, 'Should return a Food Entity.');
        $this->assertInstanceOf(NutrientInfo::class, $foodEntity->getNutrients(), 'Should return a NutrientInfo Entity');
        $this->assertNotEmpty($foodEntity->getName(), 'Name Should be not empty');
        $this->assertNotEmpty($foodEntity->getId()->getValue(), 'FoodId Should be not empty');
        $this->assertSame(FoodSource::OPEN_FOOD_FACTS->value, $foodEntity->getSource()->value, 'Should be the same Source');
    }

    /**
     * Tests the throw of Exception with invalid data source without product code.
     *
     * Expects:
     * - Exception of InvalidFoodDataException
     * - Specific message of the Exception
     */
    public function testThrowsExceptionForMissingCode(): void
    {
        $dataForFactory = [];

        $this->expectException(InvalidFoodDataException::class);
        $this->expectExceptionMessage('Product NOT_FOUND has missing required fields: code');

        $foodEntity = $this->openFoodFactsFactory->createFromArray($dataForFactory);
    }

    /**
     * Tests the throw of Exception with invalid data source without product informations.
     *
     * Expects:
     * - Exception of InvalidFoodDataException
     * - Specific message of the Exception
     */
    public function testThrowsExceptionForMissingProductName(): void
    {
        $dataForFactory = [
            'code' => '123',
            'product' => [],
        ];

        $this->expectException(InvalidFoodDataException::class);
        $this->expectExceptionMessage('Product 123 has missing required fields: product_name, nutriments, nutriments-energy-kcal_100g, nutriments-proteins_100g, nutriments-carbohydrates_100g, nutriments-fat_100g, nutriments-fiber_100g, nutriments-sodium_100g');

        $foodEntity = $this->openFoodFactsFactory->createFromArray($dataForFactory);
    }
}
