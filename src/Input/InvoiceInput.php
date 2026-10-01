<?php

namespace App\Input;

use Symfony\Component\Validator\Constraints as Assert;

final class InvoiceInput
{
    /** @param list<array{label: string, amount: int}> $lines */
    public function __construct(
        #[Assert\NotNull]
        public readonly ?int $customerId = null,
        #[Assert\Count(min: 1)]
        public readonly array $lines = [],
    ) {}
}
