<?php

namespace App\Domain\Customer;

interface CustomerRepositoryInterface
{
    public function findByEmail(string $email): ?string;
    public function findByCpf(string $document): ?string;
    public function findById(string $document): ?\App\Models\Customer;
    public function save(array $data): void;
    public function saveBalance(int $amount, string $userId): void;
}
