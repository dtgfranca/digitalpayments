<?php

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidDocumentException;

final class Document {
    private function __construct(
        private string $value,
        private DocumentType $type
    ) {}

    public static function from(string $document): self
    {
        $normalized = preg_replace('/\D/', '', $document);

        if (self::isCpf($normalized)) {
            return new self($normalized, DocumentType::CPF);
        }

        if (self::isCnpj($normalized)) {
            return new self($normalized, DocumentType::CNPJ);
        }

        throw new InvalidDocumentException('Document Invalid');
    }

    public function value(): string
    {
        return $this->value;
    }

    public function type(): DocumentType
    {
        return $this->type;
    }

    private static function isCpf(string $value): bool
    {
        // remove tudo que não for número
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) !== 11) {
            return false;
        }

        // rejeita CPFs com todos os dígitos iguais (ex: 11111111111)
        if (preg_match('/^(\d)\1{10}$/', $value)) {
           return false;
        }

        // valida dígitos verificadores
        if (! self::isValidVerifier($value)) {
            return false;
        }

        return true;
    }

    private static function isCnpj(string $value): bool
    {
        return strlen($value) === 14;
        // idem
    }

    private static function isValidVerifier($cpf)
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
}
