<?php

namespace App\Domain\Transfer;

interface TransactionMangerInterface
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;
}
