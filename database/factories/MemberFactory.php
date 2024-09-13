<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $memberNumber = 1;
        return [
            'member_no' => $memberNumber++,
            'name' => fake()->name(),
            'contact' => fake()->unique()->regexify('0722[0-9]{6}'),
            'birth_date' => fake()->dateTimeBetween('-30 years','-18 years')->format('Y-m-d'),
            'id_number' => fake()->unique()->numberBetween(1111111,3333333),
            'joining_date' => fake()->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['active', 'inactive','departed','deceased']),
        ];
    }
}
