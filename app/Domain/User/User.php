<?php

namespace App\Domain\User;

use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\ValueObjects\Amount;
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
        private readonly Document $document,
        private readonly Email $email,
        private readonly Wallet $wallet,
        private readonly UserType $type

    ) {}

    public function balance(): float
    {

        return $this->wallet->balance();
    }
    public function debit(Amount $amount): void
    {
        if($this->balance() < $amount->toFloat()) {
            throw new InsuficientFundsException('Insufficient funds');
        }
        $this->wallet->subtract($amount);
    }
    public function credit(Amount $amount): void
    {
         $this->wallet->add($amount);
    }
    public function getTypeUser(): string
    {
        return $this->type->value;
    }

}
