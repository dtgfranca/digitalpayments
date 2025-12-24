<?php

namespace App\Domain\Wallet;

use App\Domain\ValueObjects\Amount;

final class WalletMemento
{
    public function __construct(
        private Amount $balance
    ) {}

    public function balance(): Amount
    {
        return $this->balance;
    }
}
