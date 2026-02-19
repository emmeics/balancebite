<?php

namespace App\Tests\Integration\Infrastructure\ExternalService\OpenFoodFacts;

use App\Domain\Nutrition\Entity\Food;
use App\Domain\Nutrition\Exception\InvalidFoodDataException;
use App\Domain\Nutrition\ValueObject\FoodId;
use App\Domain\Nutrition\ValueObject\FoodSource;
use App\Infrastructure\ExternalService\OpenFoodFacts\OpenFoodFactsClient;
use App\Infrastructure\ExternalService\OpenFoodFacts\OpenFoodFactsFoodFactory;
use App\Infrastructure\ExternalService\OpenFoodFacts\OpenFoodFactsProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenFoodFactsProviderTest extends KernelTestCase
{
    private OpenFoodFactsClient $client;
    private OpenFoodFactsFoodFactory $factory;
    private OpenFoodFactsProvider $provider;

    /**
     * Set up test environment before each test.
     *
     * Initializes:
     * - OpenFoodFactsClient: to read informations from datasource
     * - OpenFoodFactsFoodFactory: the factory that transform the output of the API
     */
    protected function setUp(): void
    {
        $container = self::getContainer();
        $httpClient = $container->get(HttpClientInterface::class);
        $this->client = new OpenFoodFactsClient($httpClient);
        $this->factory = new OpenFoodFactsFoodFactory();
        $this->provider = new OpenFoodFactsProvider($this->client, $this->factory);
        parent::setUp();
    }

    /**
     * Tests the correct work of method "findById" with a valid FoodId.
     *
     * Expects:
     * - Return a valid Food Entity
     * - Return a Food Entity with correct data
     */
    public function testFindByIdReturnsFood(): void
    {
        $foodId = new FoodId('3017620422003');
        $result = $this->provider->findById($foodId);

        $this->assertInstanceOf(Food::class, $result, 'Should be a Food Object');
        $this->assertSame($foodId->getValue(), $result->getId()->getValue(), 'The FoodId should be the same');
        $this->assertSame('Nutella', $result->getName(), 'The name of product should be Nutella');
        $this->assertSame(FoodSource::OPEN_FOOD_FACTS->value, $result->getSource()->value, 'Should be the same source');
    }

    /**
     * Tests the correct work of method "findById" with an invalid FoodId.
     *
     * Expects:
     * - Return a null result
     */
    public function testFindByIdReturnsNullForInvalidId(): void
    {
        $foodId = new FoodId('99999999999999');
        $result = $this->provider->findById($foodId);

        $this->assertSame(null, $result, 'Should be null with an invalid FoodId');
    }

    /**
     * Tests the correct work of method "findById" with an invalid Result.
     *
     * Expects:
     * - Throw new InvalidFoodDataException
     */
    public function testFindByIdThrowsExceptionForInvalidData(): void
    {
        $mockClient = $this->createMock(OpenFoodFactsClient::class);
        $mockClient->expects($this->once())
        ->method('getById')
        ->with('99999999999999')
        ->willReturn([
            'code' => '123',
            'product' => [
            ],
        ]);

        $provider = new OpenFoodFactsProvider($mockClient, $this->factory);
        $foodId = new FoodId('99999999999999');

        $this->expectException(InvalidFoodDataException::class);

        $result = $provider->findById($foodId);
    }
}
