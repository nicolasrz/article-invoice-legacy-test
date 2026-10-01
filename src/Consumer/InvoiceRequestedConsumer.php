<?php

namespace App\Consumer;

use App\Service\InvoiceService;

final class InvoiceRequestedConsumer
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function consume(string $payload): void
    {
        $this->invoiceService->createInvoice(json_decode($payload, true));
    }
}
