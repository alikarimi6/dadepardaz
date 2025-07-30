<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
use App\States\Payment\Requested;
use App\States\Payment\RejectedByOwner;
use App\States\Payment\VerifiedByOwner;
use App\States\Payment\VerifiedBySupervisor;
use App\States\Payment\RejectedBySupervisor;
use App\States\Payment\Paid;
use App\States\Payment\PayError;
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
            'user_id' => User::factory(),
            'category_id' => ExpenseCategory::query()->inRandomOrder()->first()->id,
            'amount' => fake()->numberBetween(10000, 1000000) ,
            'state' => Requested::$name,
            'iban' => $this->faker->iban(),
            'rejection_comment' => null,
            'description' =>  fake()->optional(0.5)->sentence,
            'paid_at' => null,
        ];
    }
}
