<?php

namespace App\Application\DTO;

use App\Domain\Transfer\Transfer;

class TransferOutputDTO
{
    public function __construct(
        public string $id,
        public string $payer_id,
        public string $payee_id,
        public int $amount
    ) {}

    public static function fromTransfer(Transfer $transfer): self
    {

        return new self(
            id: $transfer->id()->value(),
            payer_id: $transfer->payer()->getUuid()->value(),
            payee_id: $transfer->payee()->getUuid()->value(),
            amount: $transfer->amount()->value()
        );
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
