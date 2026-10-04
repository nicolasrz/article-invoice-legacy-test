<?php

namespace App\Tests;

use App\Client\TaxApiClient;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use App\Service\InvoiceService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class CreateInvoiceMockedTest extends TestCase
{
    private CustomerRepository&MockObject $customers;
    private TaxApiClient&MockObject $taxApi;
    private EntityManagerInterface&MockObject $em;
    private LoggerInterface&MockObject $logger;
    private InvoiceService $service;

    protected function setUp(): void
    {
        $this->customers = $this->createMock(CustomerRepository::class);
        $this->taxApi = $this->createMock(TaxApiClient::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new InvoiceService(
            $this->createStub(InvoiceRepository::class),
            $this->customers,
            $this->taxApi,
            $this->em,
            $this->logger,
        );
    }

    public function testCreatesADraftInvoiceWithItsLinesTotalAndVat(): void
    {
        $this->customers->expects($this->once())->method('find')->with(42)->willReturn($this->customer('FR'));
        $this->taxApi->expects($this->once())->method('computeVat')->with(150_000, 'FR')->willReturn(30_000);
        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Invoice::class));
        $this->em->expects($this->once())->method('flush');
        $this->logger->expects($this->once())->method('info')->with('Facture créée');

        $invoice = $this->service->createInvoice([
            'customer_id' => 42,
            'lines' => [
                ['label' => 'Développement', 'amount' => 100_000],
                ['label' => 'Recette', 'amount' => 50_000],
            ],
        ]);

        self::assertSame('draft', $invoice->getStatus());
        self::assertSame(150_000, $invoice->getTotal());
        self::assertSame(30_000, $invoice->getVatAmount());
        self::assertCount(2, $invoice->getLines());
    }

    public function testVatDependsOnTheCustomerCountry(): void
    {
        $this->customers->expects($this->once())->method('find')->with(42)->willReturn($this->customer('DE'));
        $this->taxApi->expects($this->once())->method('computeVat')->with(100_000, 'DE')->willReturn(19_000);
        $this->em->expects($this->once())->method('flush');
        $this->logger->expects($this->once())->method('info');

        $invoice = $this->service->createInvoice([
            'customer_id' => 42,
            'lines' => [['label' => 'Développement', 'amount' => 100_000]],
        ]);

        self::assertSame(19_000, $invoice->getVatAmount());
    }

    public function testCreatesAnInvoiceWithoutCustomerWithFrenchVat(): void
    {
        $this->customers->expects($this->never())->method('find');
        $this->taxApi->expects($this->once())->method('computeVat')->with(100_000, 'FR')->willReturn(20_000);
        $this->em->expects($this->once())->method('flush');
        $this->logger->expects($this->once())->method('info');

        $invoice = $this->service->createInvoice([
            'lines' => [['label' => 'Développement', 'amount' => 100_000]],
        ]);

        self::assertSame(20_000, $invoice->getVatAmount());
    }

    private function customer(string $country): Customer
    {
        return (new Customer())->setName('ACME')->setCountry($country);
    }
}
