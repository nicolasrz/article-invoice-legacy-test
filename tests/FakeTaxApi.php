<?php

namespace App\Tests;

use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class FakeTaxApi
{
    private const RATES = ['FR' => 20, 'DE' => 19];

    public function __invoke(string $method, string $url, array $options): JsonMockResponse
    {
        $body = json_decode($options['body'], true);

        return new JsonMockResponse(['vat' => intdiv($body['amount'] * self::RATES[$body['country']], 100)]);
    }
}
