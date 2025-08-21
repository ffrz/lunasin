<?php

/**
 * MIT License
 * 
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * See LICENSE file in the project root for full license information.
 * 
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 */

namespace Database\Factories;

use App\Models\Party;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Party>
 */
class PartyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $address = $this->faker->address();
        return [
            'user_id' => $this->faker->randomElement([1, 2]),
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement(array_keys(Party::Types)),
            'phone' => $this->faker->phoneNumber(),
            'address' => $address,
            'balance' => 0.,
            'active' => true,
        ];
    }
}
