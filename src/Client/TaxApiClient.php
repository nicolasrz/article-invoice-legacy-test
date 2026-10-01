<?php

namespace App\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TaxApiClient
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $baseUrl = 'https://tax-api.example.com',
    ) {}

    public function computeVat(int $amount, string $country): int
    {
        $response = $this->http->request('POST', $this->baseUrl . '/vat', [
            'json' => ['amount' => $amount, 'country' => $country],
        ]);

        return $response->toArray()['vat'];
    }
}
