<?php

namespace App\Application;

use App\Domain\Exceptions\TransferNotAllowedException;
use App\Domain\Transfer\AuthorizerInterface;
use App\Domain\Transfer\Transfer;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\UserType;

class TranferMoney
{
    public function __construct(private readonly AuthorizerInterface $authorizer)
    {

    }

    public function execute(User $payer, User $payee, Amount $amount): void
    {
        if(!$payer->canSendMoney()) {
            throw new TransferNotAllowedException('Merchant profiles cannot make transfers, only receive them.');
        }
        if(!$this->authorizer->authorize()){
            throw new TransferNotAllowedException('Transfer not allowed.');
        }

        $transfer = new Transfer(
            payer: $payer,
            payee: $payee,
            amount: $amount,
        );
        $transfer->commit();

    }
}
