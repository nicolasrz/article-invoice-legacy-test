<?php

namespace App\Service;

final class PdfService
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly PdfRenderer $renderer,
    ) {}

    public function generate(int $invoiceId): string
    {
        $invoice = $this->invoiceService->getInvoice($invoiceId);
        $this->invoiceService->recalculateTotal($invoice);

        return $this->renderer->render($invoice);
    }
}
