<?php

namespace App\Domain\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?string;
    public function findByCpf(string $cpf): ?string;
    public function save(array $user): void;
}
