<?php

namespace App\Domain\Transfer;

interface TransferRepositoryInterface
{
    public function save(array $transfer): void;
}
