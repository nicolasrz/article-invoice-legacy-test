<?php

namespace App\Controller;

use App\Input\ApplyDiscountInput;
use App\Input\InvoiceInput;
use App\Service\InvoiceService;
use App\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoices')]
final class InvoiceController extends AbstractController
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] InvoiceInput $input): JsonResponse
    {
        $invoice = $this->invoiceService->createInvoice([
            'customer_id' => $input->customerId,
            'lines' => $input->lines,
        ]);

        return $this->json(['id' => $invoice->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}/discount', methods: ['POST'])]
    public function applyDiscount(int $id, #[MapRequestPayload] ApplyDiscountInput $input): Response
    {
        $this->invoiceService->applyDiscount($id, $input->amount, $input->reason);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/issue', methods: ['POST'])]
    public function issue(int $id): Response
    {
        $this->invoiceService->issueInvoice($id);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/pdf', methods: ['GET'])]
    public function pdf(int $id, PdfService $pdfService): Response
    {
        return new Response($pdfService->generate($id));
    }
}
