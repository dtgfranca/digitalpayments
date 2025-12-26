<?php

namespace App\Application;

class GetWalletBalance
{
    public function __construct(
        private WalletBalanceReadRepository $repository
    ) {}

    public function execute(string $userId): WalletBalanceView
    {
        return $this->repository->getByUserId($userId);
    }
}
