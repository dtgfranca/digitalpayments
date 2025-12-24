<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\Re;

class UserRepository implements UserRepositoryInterface
{

    public function findByEmail(string $email): ?string
    {
        return User::where('email', $email)->select('email')->first();
    }

    public function findByCpf(string $cpf): ?string
    {
        return User::where('document', $cpf)->select('document')->first();
    }

    public function findById(string $id): ?User
    {
         return User::where('id', $id)->first();
    }

    public function save(array $user): void
    {
        User::create($user);
    }
}
