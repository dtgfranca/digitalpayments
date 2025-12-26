<?php

namespace App\Application;

use App\Domain\Wallet\WalletBalanceReadRepositoryInterface;

class GetWalletBalance
{
    public function __construct(
        private WalletBalanceReadRepositoryInterface $repository
    ) {}

    public function execute(string $userId)
    {
        return $this->repository->getBalance($userId);
    }
}
