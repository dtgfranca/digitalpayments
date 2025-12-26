<?php

namespace Database\Factories;

use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        return [
            'payer_id' => $this->faker->word(),
            'payee_id' => $this->faker->word(),
            'amount' => $this->faker->word(),
        ];
    }
}
