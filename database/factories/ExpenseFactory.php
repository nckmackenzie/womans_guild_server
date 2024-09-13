<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use App\Models\VoteHead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeThisYear(),
            'votehead_id' => VoteHead::inRandomOrder()->first()->id,
            'amount' => fake()->randomFloat(0,1000,50000),
            'payment_method' => fake()->randomElement(['cash', 'mpesa', 'cheque','bank']),
            'payment_reference' => fake()->swiftBicNumber(),
            'reference' => fake()->swiftBicNumber(),
            'member_id' => Member::inRandomOrder()->first()->id,
            'description' => fake()->sentence(10),
            'user_id' => User::inRandomOrder()->first()->id
        ];
    }
}
