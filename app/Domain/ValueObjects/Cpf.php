<?php

namespace App\Domain\ValueObjects;

final class Cpf
{
    private string $value;

    public function __construct(string $value)
    {
        // remove tudo que não for número
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) !== 11) {
            throw new InvalidArgumentException('CPF must contain exactly 11 digits.');
        }

        // rejeita CPFs com todos os dígitos iguais (ex: 11111111111)
        if (preg_match('/^(\d)\1{10}$/', $value)) {
            throw new InvalidArgumentException('Invalid CPF.');
        }

        // valida dígitos verificadores
        if (! $this->isValid($value)) {
            throw new \InvalidArgumentException('Invalid CPF.');
        }

        $this->value = $value;
    }

    private function isValid(string $cpf): bool
    {
        // valida primeiro dígito
        $sum = 0;
        for ($i = 0, $weight = 10; $i < 9; $i++, $weight--) {
            $sum += (int) $cpf[$i] * $weight;
        }

        $digit1 = (11 - ($sum % 11));
        $digit1 = $digit1 >= 10 ? 0 : $digit1;

        if ((int) $cpf[9] !== $digit1) {
            return false;
        }

        // valida segundo dígito
        $sum = 0;
        for ($i = 0, $weight = 11; $i < 10; $i++, $weight--) {
            $sum += (int) $cpf[$i] * $weight;
        }

        $digit2 = (11 - ($sum % 11));
        $digit2 = $digit2 >= 10 ? 0 : $digit2;

        return (int) $cpf[10] === $digit2;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function formatted(): string
    {
        return substr($this->value, 0, 3).'.'.
            substr($this->value, 3, 3).'.'.
            substr($this->value, 6, 3).'-'.
            substr($this->value, 9, 2);
    }

    public function equals(Cpf $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->formatted();
    }
}
