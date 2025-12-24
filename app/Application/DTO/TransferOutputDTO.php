<?php

namespace App\Application\DTO;

use App\Domain\Transfer\Transfer;

class TransferOutputDTO
{
    public function __construct(
        public string $id,
        public string $payerId,
        public string $payeeId,
        public int $amount
    ) {}

    public static function fromTransfer(Transfer $transfer): self
    {

        return new self(
            id: $transfer->id()->value(),
            payerId: $transfer->payer()->getUuid()->value(),
            payeeId: $transfer->payee()->getUuid()->value(),
            amount: $transfer->amount()->value()
        );
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
