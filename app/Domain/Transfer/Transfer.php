<?php

namespace App\Domain\Transfer;

use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;

class Transfer
{
    public function __construct(
        private User $payer,
        private User $payee,
        private Amount $amount,

    ) {}

    public function commit(): void
    {
        $this->payer->wallet()->debit($this->amount);
        $this->payee->wallet()->credit($this->amount);

    }

}
