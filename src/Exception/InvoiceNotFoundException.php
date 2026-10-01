<?php

namespace App\Exception;

class InvoiceNotFoundException extends \RuntimeException
{
    public function __construct(int $invoiceId)
    {
        parent::__construct(sprintf('Facture %d introuvable', $invoiceId));
    }
}
