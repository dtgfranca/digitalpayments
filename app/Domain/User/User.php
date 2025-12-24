<?php

namespace App\Domain\User;

use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;


class User extends UserRegular
{
    public function __construct(
        private readonly Password $uuid,
        private readonly string $fullname,
        private readonly Cpf $document,
        private readonly Email $email,
        private readonly Wallet $wallet,
        private readonly UserType $type

    ) {}

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
}
