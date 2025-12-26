<?php

namespace App\Domain\ValueObjects;

final class Uuid
{
    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));

        if (! self::isValid($value)) {
            throw new \InvalidArgumentException('Invalid UUID.');
        }

        $this->value = $value;
    }

    /**
     * Factory helper (opcional, mas muito útil)
     */
    public static function generate(): self
    {
        return new self(self::v4());
    }

    private static function isValid(string $uuid): bool
    {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid
        );
    }

    private static function v4(): string
    {
        $data = random_bytes(16);

        // versão 4
        $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
        // variante RFC 4122
        $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Uuid $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
