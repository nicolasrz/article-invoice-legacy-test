<?php

namespace App\Service;

use App\Entity\Invoice;

class PdfRenderer
{
    public function render(Invoice $invoice): string
    {
        $html = sprintf('<h1>Facture n°%d</h1><ul>', $invoice->getId());
        foreach ($invoice->getLines() as $line) {
            $html .= sprintf('<li>%s : %.2f €</li>', $line->getLabel(), $line->getAmount() / 100);
        }

        return $html . sprintf(
            '</ul><p>Total HT : %.2f €</p><p>TVA : %.2f €</p>',
            $invoice->getTotal() / 100,
            $invoice->getVatAmount() / 100,
        );
    }
}
