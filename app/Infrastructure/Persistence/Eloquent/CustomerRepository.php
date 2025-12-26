<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use App\Models\Customer;
use App\Models\Wallet;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findByEmail(string $email): ?string
    {
        return Customer::where('email', $email)->select('email')->first();
    }

    public function findByCpf(string $cpf): ?string
    {
        return Customer::where('document', $cpf)->select('document')->first();
    }

    public function findById(string $id): ?Customer
    {
        return Customer::where('id', $id)->first();
    }

    public function save(array $data): void
    {
        $customer = Customer::create($data);

        Wallet::create([
            'id' => Uuid::generate(),
            'customer_id' => $customer->id,
            'balance' => $data['balance'],
        ]);

    }

    public function saveBalance(int $amount, string $userId): void
    {
        Wallet::where('customer_id', $userId)->update(['balance' => $amount]);
    }
}
