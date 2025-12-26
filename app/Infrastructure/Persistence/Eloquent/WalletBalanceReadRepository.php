<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Wallet\WalletBalanceReadRepositoryInterface;
use App\Models\Wallet;

class WalletBalanceReadRepository implements WalletBalanceReadRepositoryInterface
{
    public function getBalance(string $userId): Wallet
    {
        return Wallet::where('customer_id', $userId)->first();
    }
}
