<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Transfer\TransactionMangerInterface;
use App\Domain\Transfer\TransferRepositoryInterface;
use App\Models\Transfer;

class TransferRepository implements TransferRepositoryInterface
{

    public function save(array $transfer): void
    {

        Transfer::create($transfer);

    }
}
