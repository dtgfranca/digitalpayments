<?php

namespace Database\Factories;

use App\Domain\ValueObjects\UserType;
use App\Domain\ValueObjects\Uuid;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'id'=>Uuid::generate(),
            'fullname' => $this->faker->word(),
            'document' => $this->faker->randomNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password()),
            'type' => UserType::REGULAR,
        ];
    }
}
