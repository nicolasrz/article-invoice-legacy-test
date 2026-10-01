<?php

namespace App\Api;

use App\Service\InvoiceService;

final class IssueInvoiceProcessor
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function process(int $invoiceId): void
    {
        $this->invoiceService->issueInvoice($invoiceId);
    }
}
