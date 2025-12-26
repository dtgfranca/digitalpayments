<?php

namespace App\Domain\ValueObjects;

class Amount
{
    public function __construct(private readonly int $value)
    {
        if ($value < 0) {
            throw new \Exception('Amount must be positive');
        }

    }

    public static function fromFloat(int $value): self
    {
        return new self((int) round($value * 100));
    }

    public static function toCents(float $value): int
    {
        if (! preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
            throw new \InvalidArgumentException('Invalid money format');
        }

        return (int) round($value * 100);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function toFloat(): float
    {
        return $this->value / 100;
    }
}
