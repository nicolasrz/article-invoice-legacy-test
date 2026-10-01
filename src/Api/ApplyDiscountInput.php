<?php

namespace App\Api;

final class ApplyDiscountInput
{
    public function __construct(
        public readonly ?int $amount = null,
        public readonly ?string $reason = null,
    ) {}
}
