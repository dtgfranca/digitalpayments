<?php

namespace App\Domain\Wallet;

use App\Models\Wallet;

interface WalletBalanceReadRepositoryInterface
{
    public function getBalance(string $userId): Wallet;
}
