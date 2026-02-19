<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalService\OpenFoodFacts;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Entity representing an http wrapper for OpenFoodFacts data source.
 *
 * First goal is to read and search data comes from external OpenFoodFacts APIs.
 */
class OpenFoodFactsClient
{
    private const BASEURL = 'https://world.openfoodfacts.org';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Search food on OpenFoodFacts API through a query string.
     *
     * @param string $query The query string to search
     * @param int    $limit The limit of item showed in the search, by default 20
     *
     * @return array<mixed> The array of the item results
     */
    public function search(string $query, int $limit = 20): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASEURL.'/cgi/search.pl',
            [
                'query' => ['search_terms' => $query, 'json' => 1, 'page_size' => $limit],
            ]
        );

        try {
            $data = $response->toArray();
        } catch (TransportExceptionInterface|ClientExceptionInterface $e) {
            return [];
        }

        return $data;
    }

    /**
     * Get a food on OpenFoodFacts API through his ID.
     *
     * @param string $id The id of the food
     *
     * @return array<mixed> The array of the food result
     */
    public function getById(string $id): ?array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASEURL.'/api/v2/product/'.$id.'.json'
        );

        try {
            $data = $response->toArray();
        } catch (TransportExceptionInterface|ClientExceptionInterface $e) {
            return null;
        }

        return $data;
    }
}
