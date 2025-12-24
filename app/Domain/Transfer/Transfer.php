<?php

namespace App\Domain\Transfer;

use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Uuid;

class Transfer
{
    private function __construct(
        private Uuid $id,
        private User $payer,
        private User $payee,
        private Amount $amount,

    ) {}
    public function id(): Uuid
    {
        return $this->id;
    }

    public function payer(): User
    {
        return $this->payer;
    }

    public function payee(): User
    {
        return $this->payee;
    }

    public function amount(): Amount
    {
        return $this->amount;
    }
    public static function create(User $payer, User $payee, Amount $amount): self
    {
        return new self(
            Uuid::generate(),
            $payer,
            $payee,
            $amount
        );
    }
    public function commit(): void
    {
        $this->payer->wallet()->debit($this->amount);
        $this->payee->wallet()->credit($this->amount);

    }

}
