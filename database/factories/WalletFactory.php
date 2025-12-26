<?php

namespace Database\Factories;

use App\Domain\ValueObjects\Uuid;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'id' => Uuid::generate(),
            'customer_id' => $this->faker->randomNumber(),
            'balance' => $this->faker->randomNumber(),
        ];
    }
}
