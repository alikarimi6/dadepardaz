<?php

namespace Tests\Feature;

use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\States\Payment\Requested;
use App\States\Payment\VerifiedByOwner;
use App\States\Payment\VerifiedBySupervisor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'supervisor']);
        Role::firstOrCreate(['name' => 'owner']);

        // Create default expense category
        ExpenseCategory::factory()->create();
    }

    public function test_owner_can_create_expense_with_attachment()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('owner');

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('receipt.jpg');

        $response = $this->postJson(route('expenses.store'), [
            'amount' => 100000,
            'category_id' => ExpenseCategory::first()->id,
            'description' => 'Lunch',
            'iban' => 'IR123456789012345678901234',
            'paid_at' => now()->toDateString(),
            'attachments' => [$file],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('expenses', [
            'description' => 'Lunch',
            'amount' => 100000,
        ]);
    }

    public function test_supervisor_can_approve_expense()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $expense = Expense::factory()->create([
            'state' => Requested::$name,
            'iban' => 'IR123456789012345678901234',
            'user_id' => $user->id,
            'payment_method'=> 'manual'
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('expenses.approve', $expense));

        $response->assertOk();
        $this->assertEquals(VerifiedBySupervisor::class, $expense->fresh()->state);
        Event::assertDispatched(ExpenseApproved::class);
    }

    public function test_owner_can_reject_expense()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('owner');

        $expense = Expense::factory()->create([
            'state' => VerifiedBySupervisor::$name,
            'iban' => 'IR123456789012345678901234',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('expenses.reject', $expense), [
            'rejection_comment' => 'Invalid expense',
        ]);

        $response->assertOk();
        $this->assertNotNull($expense->fresh()->rejection_comment);
        Event::assertDispatched(ExpenseRejected::class);
    }

    public function test_bulk_approve_expenses()
    {
        $user = User::factory()->create();
        $user->assignRole('owner');

        $expenses = Expense::factory()->count(3)->create([
            'state' => VerifiedBySupervisor::$name,
            'iban' => 'IR123456789012345678901234',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('expenses.bulkApprove'), [
            'ids' => $expenses->pluck('id')->toArray(),
        ]);

        $response->assertOk();
        $expenses->each(function ($expense) {
            $this->assertEquals(VerifiedByOwner::class, $expense->fresh()->state);
        });
    }

    public function test_bulk_reject_expenses()
    {
        $user = User::factory()->create();
        $user->assignRole('owner');

        $expenses = Expense::factory()->count(2)->create([
            'state' => VerifiedBySupervisor::$name,
            'iban' => 'IR123456789012345678901234',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('expenses.bulkReject'), [
            'ids' => $expenses->pluck('id')->toArray(),
            'rejection_comment' => 'Invalid bulk expenses',
        ]);

        $response->assertOk();
        $expenses->each(function ($expense) {
            $this->assertNotNull($expense->fresh()->rejection_comment);
        });
    }

    public function test_get_expense_list()
    {
        $user = User::factory()->create();
        $user->assignRole('owner');
        $this->actingAs($user);

        Expense::factory()->count(5)->create([
            'iban' => 'IR123456789012345678901234',
            'user_id' => $user->id,
        ]);

        $response = $this->getJson(route('expenses.index'));
        $response->assertOk();
        $response->assertJsonStructure(['data']);
    }

    public function test_get_expense_details()
    {
        $user = User::factory()->create();
        $user->assignRole('owner');

        $this->actingAs($user);

        $expense = Expense::factory()->create([
            'iban' => 'IR123456789012345678901234',
            'user_id' => $user->id,
        ]);

        $response = $this->getJson(route('expenses.show', $expense));
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $expense->id
        ]);
    }

    public function test_delete_expense()
    {
        $user = User::factory()->create();
        $user->assignRole('owner');

        $this->actingAs($user);

        $expense = Expense::factory()->create([
            'iban' => 'IR123456789012345678901234',
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson(route('expenses.destroy', $expense));
        $response->assertNoContent();

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
