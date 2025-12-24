<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Transfer\TransactionMangerInterface;
use Illuminate\Support\Facades\DB;

class LaravelManagerTransaction implements TransactionMangerInterface
{

    public function begin(): void
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollback(): void
    {
        DB::rollBack();
    }
}
