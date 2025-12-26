<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Wallet;

class WalletBalanceReadRepository
{
    public function getBalance(string $userId): Wallet {
        return  Wallet::where('customer_id', $userId)->first();
    }
}
