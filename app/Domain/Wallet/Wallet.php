<?php

namespace App\Domain\Wallet;

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
    public function subtract(Amount $amount):void
    {
       $this->balance =  $this->amount->toFloat() - $amount->toFloat();
    }
    public function add(Amount $amount):void
    {
        $this->balance = $this->amount->toFloat() + $amount->toFloat();
    }



}
