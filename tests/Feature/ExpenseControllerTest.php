<?php

namespace Tests\Feature;

use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Services\Expense\ExpenseStateService;
use App\States\Payment\Requested;
use App\States\Payment\VerifiedBySupervisor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected ExpenseStateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExpenseStateService();
        foreach (['owner', 'supervisor'] as $role) {
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
            }
        }
        ExpenseCategory::factory()->count(5)->create();

    }

    public function test_tryApproveExpense_allows_owner_with_correct_state()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('owner');

        $expense = Expense::factory()->create([
            'state' => VerifiedBySupervisor::$name,
        ]);
        $expense->state = new VerifiedBySupervisor($expense);
        $response = $this->actingAs($user)
            ->postJson(route('expenses.approve', [
                'expense' => $expense->id ,
                'payment_method' => 'manual',
                ]))
            ->assertOk();
        Event::assertDispatched(ExpenseApproved::class);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_tryApproveExpense_denies_owner_with_wrong_state()
    {
        Event::fake();
        $user = User::factory()->create();
        $user->assignRole('owner');

        $expense = Expense::factory()->create([
            'state' => Requested::$name,
        ]);
        $expense->state = new Requested($expense);
        $response = $this->actingAs($user)
            ->postJson(route('expenses.approve', [
                'expense' => $expense->id ,
                'payment_method' => 'manual',
            ]))
            ->assertForbidden();
        Event::assertNotDispatched(ExpenseApproved::class);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_tryRejectExpense_allows_supervisor_with_correct_state()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $expense = Expense::factory()->create([
            'state' => Requested::$name,
        ]);
        $expense->state = new Requested($expense);

        $response = $this->actingAs($user)
            ->postJson(route('expenses.reject', [
                'expense' => $expense->id ,
                'rejection_comment' => 'test_test',
            ]))
            ->assertOk();

        Event::assertDispatched(ExpenseRejected::class);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_tryRejectExpense_denies_supervisor_with_wrong_state()
    {
        Event::fake();
        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $expense = Expense::factory()->create([
            'state' => VerifiedBySupervisor::$name,
        ]);
        $expense->state = new VerifiedBySupervisor($expense);
        $response = $this->actingAs($user)
            ->postJson(route('expenses.reject', [
                'expense' => $expense->id ,
                'rejection_comment' => 'test_test',
            ]));
        Event::assertNotDispatched(ExpenseRejected::class);
        $this->assertEquals(403, $response->getStatusCode());
    }
    public function test_bulk_approve_expenses()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $expenses = Expense::factory()->count(2)->create([
            'state' => Requested::$name,
        ]);

        $this->actingAs($user)
            ->postJson(route('expenses.bulk.approve'), [
                'ids' => $expenses->pluck('id')->toArray(),
                'payment_method' => 'manual',
            ])
            ->assertOk();

        Event::assertDispatchedTimes(ExpenseApproved::class, 2);

        foreach ($expenses as $expense) {
            Event::assertDispatched(ExpenseApproved::class, function ($event) use ($expense) {
                return $event->expense->id === $expense->id;
            });
        }
    }
    public function test_bulk_approve_returns_404_if_expense_not_found()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('owner');

        $this->actingAs($user)
            ->postJson(route('expenses.bulk.approve'), [
                'ids' => [999999],
                'payment_method' => 'scheduled',
            ])
            ->assertStatus(404);
        Event::assertNotDispatched(ExpenseApproved::class);
    }



    public function test_bulk_reject_expenses()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $expenses = Expense::factory()->count(2)->create([
            'state' => Requested::$name,
        ]);

        $this->actingAs($user)
            ->postJson(route('expenses.bulk.reject'), [
                'ids' => $expenses->pluck('id')->toArray(),
                'rejection_comment' => 'rejected',
            ])
            ->assertOk();

        Event::assertDispatchedTimes(ExpenseRejected::class, 2);

        foreach ($expenses as $expense) {
            Event::assertDispatched(ExpenseRejected::class, function ($event) use ($expense) {
                return $event->expense->id === $expense->id;
            });
        }
    }
    public function test_bulk_reject_returns_404_if_expense_not_found()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('owner');

        $this->actingAs($user)
            ->postJson(route('expenses.bulk.reject'), [
                'ids' => [999999],
                'rejection_comment' => 'scheduled',
            ])
            ->assertStatus(404);
        Event::assertNotDispatched(ExpenseRejected::class);
    }

    public function test_bulk_approve_denies_owner_wrong_state()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('owner');

        $expenses = Expense::factory()->count(2)->create([
            'state' => Requested::$name,
        ]);

        $this->actingAs($user)
            ->postJson(route('expenses.bulk.approve'), [
                'ids' => $expenses->pluck('id')->toArray(),
                'payment_method' => 'manual',
            ])
            ->assertForbidden();

        Event::assertNotDispatched(ExpenseApproved::class);
    }

    public function test_bulk_reject_denies_owner_wrong_state()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->assignRole('owner');

        $expenses = Expense::factory()->count(2)->create([
            'state' => Requested::$name,
        ]);

        $this->actingAs($user)
            ->postJson(route('expenses.bulk.reject'), [
                'ids' => $expenses->pluck('id')->toArray(),
                'rejection_comment' => 'test',
            ])
            ->assertForbidden();

        Event::assertNotDispatched(ExpenseRejected::class);
    }
}
