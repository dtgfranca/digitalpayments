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

    public function execute(): void
    {
//        if($this->payer->balance() < $this->amount->toFloat()) {
//            return 0.0;
//        }
        $this->payer->debit($this->amount);
        $this->payee->credit($this->amount);

    }

    public function rollback(): void {}
}
