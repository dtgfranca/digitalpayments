<?php

namespace App\Domain\Wallet;

use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\ValueObjects\Amount;

class Wallet
{
    private int $balance;

    public function __construct(private Amount $amount)
    {
        $this->balance = $amount->value();
    }

    public function balance(): float
    {

        return $this->balance;
    }

    private function subtract(Amount $amount): void
    {
        $this->balance = $this->amount->value() - $amount->value();

    }

    private function add(Amount $amount): void
    {
        $this->balance = $this->amount->value() + $amount->value();

    }

    public function debit(Amount $amount): void
    {

        if ($this->balance < $amount->value()) {
            throw new InsuficientFundsException('Insufficient funds');
        }
        $this->subtract($amount);
    }

    public function credit(Amount $amount): void
    {
        $this->add($amount);
    }

    public function createMemento(): WalletMemento
    {

        return new WalletMemento(
            new Amount($this->balance)
        );
    }

    public function restore(WalletMemento $memento): void
    {
        $this->balance = $memento->balance()->value();
    }
}
