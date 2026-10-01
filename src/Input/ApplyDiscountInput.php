<?php

namespace App\Input;

use Symfony\Component\Validator\Constraints as Assert;

final class ApplyDiscountInput
{
    public function __construct(
        #[Assert\NotNull]
        public readonly ?int $amount = null,
        #[Assert\NotBlank]
        public readonly ?string $reason = null,
    ) {}
}
