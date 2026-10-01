<?php

namespace App\Message;

final class RefundInvoice
{
    public function __construct(
        public readonly int $invoiceId,
        public readonly int $amount,
    ) {}
}
