<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\UserIban;
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
        $status = fake()->randomElement(['pending', 'rejected']);
        return [
            'user_id' => User::factory(),
            'category_id' => ExpenseCategory::query()->inRandomOrder()->first()->id,
            'amount' => fake()->numberBetween(10000, 1000000) ,
            'iban' => $this->faker->iban(),
            'state' => $status ==='pending' ? Requested::$name : RejectedByOwner::$name,
            'rejection_comment' => $status === 'rejected' ? fake()->sentence : null,
            'description' =>  fake()->optional(0.5)->sentence,
            'paid_at' => null,
        ];
    }
}
