<?php

namespace App\Domain\User;

use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Uuid;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;


class User extends UserRegular
{
    private function __construct(
        private readonly Uuid     $uuid,
        private readonly string   $fullname,
        private readonly Cpf      $document,
        private readonly Email    $email,
        private readonly Wallet   $wallet,
        private readonly UserType $type

    ) {}

    public static function create(
        string $fullname,
        Cpf $document,
        Email $email,
        Wallet $wallet,
        UserType $type
    ): self {
        return new self(
            uuid: Uuid::generate(),
            fullname: $fullname,
            document: $document,
            email: $email,
            wallet: $wallet,
            type: $type
        );
    }

    public function balance(): float
    {

        return $this->wallet->balance();
    }
    public function getTypeUser(): string
    {
        return $this->type->value;
    }
    public function canSendMoney(): bool
    {
        return $this->getTypeUser() !== UserType::MERCHANT->value;
    }
    public function wallet(): Wallet
    {
        return $this->wallet;
    }
    public function toArray(): array
    {
        return [
            'id'       => $this->uuid->value(),
            'fullname' => $this->fullname,
            'email'    => $this->email->value(),
            'document' => $this->document->value(),
            'type'     => $this->type->value,
            'balance'  => $this->wallet->balance(),
        ];
    }
}
