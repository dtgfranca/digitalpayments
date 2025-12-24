<?php

namespace App\Domain\Wallet;

use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\ValueObjects\Amount;

final class Wallet
{
    private int $balance;
    public function __construct(private  Amount $amount) {
        $this->balance = $amount->toFloat();
    }

    public function balance(): float
    {
        return $this->balance;
    }
    private function subtract(Amount $amount):void
    {
       $this->balance =  $this->amount->toFloat() - $amount->toFloat();

    }
    private function add(Amount $amount):void
    {
        $this->balance = $this->amount->toFloat() + $amount->toFloat();

    }
    public function debit(Amount $amount): void
    {

        if($this->balance < $amount->toFloat()) {
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
