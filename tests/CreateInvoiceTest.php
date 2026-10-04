<?php

namespace App\Tests;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Service\InvoiceService;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CreateInvoiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private InvoiceService $service;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(InvoiceService::class);

        $schema = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schema->dropSchema($metadata);
        $schema->createSchema($metadata);
    }

    public function testCreatesADraftInvoiceWithItsLinesTotalAndVat(): void
    {
        $customer = $this->customer('FR');

        $invoice = $this->service->createInvoice([
            'customer_id' => $customer->getId(),
            'lines' => [
                ['label' => 'Développement', 'amount' => 100_000],
                ['label' => 'Recette', 'amount' => 50_000],
            ],
        ]);

        $this->em->clear();
        $saved = $this->em->find(Invoice::class, $invoice->getId());

        self::assertSame('draft', $saved->getStatus());
        self::assertSame(150_000, $saved->getTotal());
        self::assertSame(30_000, $saved->getVatAmount());
        self::assertCount(2, $saved->getLines());
    }

    public function testVatDependsOnTheCustomerCountry(): void
    {
        $customer = $this->customer('DE');

        $invoice = $this->service->createInvoice([
            'customer_id' => $customer->getId(),
            'lines' => [['label' => 'Développement', 'amount' => 100_000]],
        ]);

        self::assertSame(19_000, $invoice->getVatAmount());
    }

    public function testRefusesAnInvoiceWithoutCustomerOnlyOnceTheDatabaseComplains(): void
    {
        $this->expectException(NotNullConstraintViolationException::class);

        $this->service->createInvoice([
            'lines' => [['label' => 'Développement', 'amount' => 100_000]],
        ]);
    }

    private function customer(string $country): Customer
    {
        $customer = (new Customer())->setName('ACME')->setCountry($country);
        $this->em->persist($customer);
        $this->em->flush();

        return $customer;
    }
}
