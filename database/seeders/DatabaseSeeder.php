<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserIban;
use Database\Factories\BankFactory;
use Database\Factories\ExpenseFactory;
use Database\Factories\UserIbanFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = ['حمل و نقل', 'ایاب و ذهاب', 'خرید تجهیزات'];
        $bankPrefixes = ['11', '22', '33'];
        $bankNames = ['بانک سرمایه' , 'بانک سامان' , 'بانک تجارت'];
        Bank::factory()
            ->sequence(...array_map(fn($prefix) => ['prefix' => $prefix], $bankPrefixes))
            ->sequence(...array_map(fn($name) => ['name' => $name] , $bankNames))
            ->count(count($bankPrefixes))
            ->create();

        ExpenseCategory::factory()
            ->sequence(...array_map(fn($category) => ['name' => $category], $categories))
            ->count(count($categories))
            ->create();

        User::factory()
            ->count(5)
            ->has(UserIban::factory()->count(3), 'ibans')
            ->afterCreating(function (User $user) {
                Expense::factory()->count(8)->create([
                    'user_id' => $user->id,
                    'iban' => $user->ibans->random()->code,
                ]);
            })
            ->create();
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    }

}
