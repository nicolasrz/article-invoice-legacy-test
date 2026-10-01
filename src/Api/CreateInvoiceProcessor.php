<?php

namespace App\Api;

use App\Entity\Invoice;
use App\Service\InvoiceService;

final class CreateInvoiceProcessor
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function process(InvoiceInput $input): Invoice
    {
        if ($input->customerId === null) {
            throw new InvalidInputException('customerId est obligatoire');
        }
        if ($input->lines === []) {
            throw new InvalidInputException('Une facture doit avoir au moins une ligne');
        }

        return $this->invoiceService->createInvoice([
            'customer_id' => $input->customerId,
            'lines' => $input->lines,
        ]);
    }
}
