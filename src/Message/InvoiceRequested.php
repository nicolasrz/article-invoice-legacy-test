<?php

namespace App\Message;

final class InvoiceRequested
{
    public function __construct(
        public readonly array $payload,
    ) {}
}
