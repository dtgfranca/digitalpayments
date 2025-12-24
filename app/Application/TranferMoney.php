<?php

namespace App\Application;

use App\Domain\Transfer\Transfer;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;

class TranferMoney
{
    public function execute(User $payer, User $payee, Amount $amount): void
    {
        $transfer = new Transfer(
            payer: $payer,
            payee: $payee,
            amount: $amount,
        );
        $transfer->execute();

    }
}
