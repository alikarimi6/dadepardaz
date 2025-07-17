<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
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

        $status = fake()->randomElement(['pending', 'approved', 'rejected']);
        return [
            'category_id' => ExpenseCategory::query()->inRandomOrder()->first()->id,
            'amount' => fake()->numberBetween(10000, 1000000) ,
            'status' => $status,
            'rejection_comment' => $status === 'rejected' ? fake()->sentence : null,
            'description' =>  fake()->optional(0.5)->sentence,
            'paid_at' => $status === 'approved' ? fake()->dateTimeBetween('now', '+10 days') : null
        ];
    }
}
