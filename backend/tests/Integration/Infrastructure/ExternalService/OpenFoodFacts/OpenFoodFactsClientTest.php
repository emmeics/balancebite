<?php

namespace App\Tests\Integration\Infrastructure\ExternalService\OpenFoodFacts;

use App\Infrastructure\ExternalService\OpenFoodFacts\OpenFoodFactsClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Integration tests for OpenFoodFacts HTTP Client Wrapper.
 *
 * Tests the requests to the OpenFoodFacts APIs:
 * - Search a food by a query string
 * - Get food data by the string id
 */
class OpenFoodFactsClientTest extends WebTestCase
{
    private OpenFoodFactsClient $openFoodFactsClient;

    /**
     * Set up test environment before each test.
     *
     * Initializes:
     * - HttpClient: HTTP Client to pass to the OpenFoodFactsClient
     * - OpenFoodFactsClient: to call methods to test
     */
    protected function setUp(): void
    {
        $container = self::getContainer();
        $httpClient = $container->get(HttpClientInterface::class);
        $this->openFoodFactsClient = new OpenFoodFactsClient($httpClient);

        parent::setUp();
    }

    /**
     * Tests the search through the query string "nutella".
     *
     * Expects:
     * - Return a valid array with data
     * - Return an array with almost a row with a key "products"
     * - Return an item in products with almost these keys: code, product_name
     */
    public function testSearchReturnsProducts(): void
    {
        $query_string = 'nutella';

        $results = $this->openFoodFactsClient->search($query_string);

        $this->assertNotEmpty($results, 'The results should be not empty');
        $this->assertArrayHasKey('products', $results, 'The results should contain a row with key "products"');
        $this->assertArrayHasKey('code', $results['products'][0], 'The first item of products results should contain a row with the key "code"');
        $this->assertArrayHasKey('product_name', $results['products'][0], 'The first item of products results should contain a row with the key "product_name"');
    }

    /**
     * Tests the search through the query string "nutella" and the page size limit.
     *
     * Expects:
     * - Return a valid array with data
     * - Return an array with 5 row contained in the  "products" row
     */
    public function testSearchWithLimitRespectsPageSize(): void
    {
        $query_string = 'nutella';
        $page_size_limit = 5;

        $results = $this->openFoodFactsClient->search($query_string, $page_size_limit);

        if (empty($results) || !isset($results['products'])) {
            $this->markTestSkipped('OpenFoodFacts API did not return results');
        }

        $this->assertNotEmpty($results, 'The results should be not empty');
        $this->assertSame(5, (int) sizeof($results['products']), 'The sizeof "products" searched must be 5');
    }

    /**
     * Tests the search of food by a valid ID.
     *
     * Expects:
     * - Return a valid array with data
     * - Return an item with almost these keys: code, product_name
     */
    public function testGetByIdReturnsProduct(): void
    {
        $food_id = '3017620422003';

        $result = $this->openFoodFactsClient->getById($food_id);

        $this->assertNotEmpty($result, 'The results should be not empty');
        $this->assertArrayHasKey('code', $result, 'The result should contain a row with the key "code"');
        $this->assertArrayHasKey('product_name', $result['product'], 'The result should contain a row with the key "product_name" in the "product" row');
    }

    /**
     * Tests the search of food by an invalid ID.
     *
     * Expects:
     * - Return a null result
     */
    public function testGetByIdReturnsNullForInvalidId(): void
    {
        $food_id = '99999999999999';

        $result = $this->openFoodFactsClient->getById($food_id);

        $this->assertNull($result, 'The results should be null');
    }
}
