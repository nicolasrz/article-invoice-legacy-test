<?php

namespace App\Api;

final class InvoiceInput
{
    /** @param list<array{label: string, amount: int}> $lines */
    public function __construct(
        public readonly ?int $customerId = null,
        public readonly array $lines = [],
    ) {}
}
