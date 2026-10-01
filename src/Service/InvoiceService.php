<?php

namespace App\Service;

use App\Client\TaxApiClient;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Exception\InvoiceNotFoundException;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepository $invoices,
        private readonly CustomerRepository $customers,
        private readonly TaxApiClient $taxApi,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {}

    public function createInvoice(array $data): Invoice
    {
        $invoice = new Invoice();
        $invoice->setCustomerId($data['customer_id'] ?? null);

        $total = 0;
        foreach ($data['lines'] ?? [] as $row) {
            $line = new InvoiceLine();
            $line->setLabel($row['label']);
            $line->setAmount($row['amount']);
            $invoice->getLines()->add($line);
            $total += $row['amount'];
        }

        $customer = $invoice->getCustomerId() !== null
            ? $this->customers->find($invoice->getCustomerId())
            : null;

        $invoice->setTotal($total);
        $invoice->setVatAmount($this->taxApi->computeVat($total, $customer?->getCountry() ?? 'FR'));

        $this->em->persist($invoice);
        $this->em->flush();
        $this->logger->info('Facture créée', ['invoice' => $invoice->getId()]);

        return $invoice;
    }

    public function applyDiscount(int $invoiceId, int $amount, string $reason): void
    {
        $invoice = $this->invoices->find($invoiceId);
        if ($invoice === null) {
            throw new InvoiceNotFoundException($invoiceId);
        }

        if ($invoice->getStatus() === 'issued') {
            throw new \LogicException('Facture déjà émise, non modifiable');
        }
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Remise invalide');
        }

        $customer = $this->customers->find($invoice->getCustomerId());
        if ($customer->getType() === 'vip' && $amount > 100_000) {
            $amount = 100_000;
        }

        $line = new InvoiceLine();
        $line->setLabel('Remise : ' . $reason);
        $line->setAmount(-$amount);

        $invoice->getLines()->add($line);

        $total = 0;
        foreach ($invoice->getLines() as $l) {
            $total += $l->getAmount();
        }

        $vat = $this->taxApi->computeVat($total, $customer->getCountry());
        $invoice->setTotal($total);
        $invoice->setVatAmount($vat);

        $this->em->flush();
        $this->logger->info('Remise appliquée', ['invoice' => $invoiceId]);
    }

    public function issueInvoice(int $invoiceId): void
    {
        $invoice = $this->getInvoice($invoiceId);

        if ($invoice->getStatus() === 'issued') {
            throw new \LogicException('Facture déjà émise');
        }
        if ($invoice->getLines()->isEmpty()) {
            throw new \LogicException('Impossible d\'émettre une facture vide');
        }

        $invoice->setStatus('issued');
        $invoice->setIssuedAt(new \DateTimeImmutable());

        $this->em->flush();
        $this->logger->info('Facture émise', ['invoice' => $invoiceId]);
    }

    public function getInvoice(int $invoiceId): Invoice
    {
        $invoice = $this->invoices->find($invoiceId);
        if ($invoice === null) {
            throw new InvoiceNotFoundException($invoiceId);
        }

        return $invoice;
    }

    public function recalculateTotal(Invoice $invoice): void
    {
        $total = 0;
        foreach ($invoice->getLines() as $line) {
            $total += $line->getAmount();
        }

        $customer = $this->customers->find($invoice->getCustomerId());

        $invoice->setTotal($total);
        $invoice->setVatAmount($this->taxApi->computeVat($total, $customer->getCountry()));

        $this->em->flush();
    }
}
