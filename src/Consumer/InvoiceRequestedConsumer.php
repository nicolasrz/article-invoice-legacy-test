<?php

namespace App\Consumer;

use App\Message\InvoiceRequested;
use App\Service\InvoiceService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class InvoiceRequestedConsumer
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function __invoke(InvoiceRequested $message): void
    {
        $this->invoiceService->createInvoice($message->payload);
    }
}
