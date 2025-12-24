<?php

namespace App\Application;

use App\Domain\Exceptions\TransferNotAllowedException;
use App\Domain\Transfer\Transfer;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\UserType;

class TranferMoney
{
    public function execute(User $payer, User $payee, Amount $amount): void
    {
        if($payer->getTypeUser() === UserType::MERCHANT->value) {
            throw new TransferNotAllowedException('Merchant profiles cannot make transfers, only receive them.');
        }

        $transfer = new Transfer(
            payer: $payer,
            payee: $payee,
            amount: $amount,
        );
        $transfer->execute();

    }
}
