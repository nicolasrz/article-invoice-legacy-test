<?php

namespace App\MessageHandler;

use App\Entity\InvoiceLine;
use App\Message\RefundInvoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;

final class RefundInvoiceHandler
{
    public function __construct(
        private readonly InvoiceRepository $invoices,
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(RefundInvoice $message): void
    {
        $invoice = $this->invoices->find($message->invoiceId);

        $line = new InvoiceLine();
        $line->setLabel('Remboursement');
        $line->setAmount(-$message->amount);
        $invoice->getLines()->add($line);

        $this->em->flush();
    }
}
