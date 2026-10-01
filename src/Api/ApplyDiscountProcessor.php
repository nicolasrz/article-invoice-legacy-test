<?php

namespace App\Api;

use App\Service\InvoiceService;

final class ApplyDiscountProcessor
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function process(ApplyDiscountInput $input, int $invoiceId): void
    {
        if ($input->amount === null) {
            throw new InvalidInputException('amount est obligatoire');
        }
        if ($input->reason === null || $input->reason === '') {
            throw new InvalidInputException('reason est obligatoire');
        }

        $this->invoiceService->applyDiscount($invoiceId, $input->amount, $input->reason);
    }
}
