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

        $response = $this->service->tryApproveExpense($user, $expense, 'manual');

        Event::assertDispatched(ExpenseApproved::class);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_tryApproveExpense_denies_owner_with_wrong_state()
    {
        $user = User::factory()->create();
        $user->assignRole('owner');

        $expense = Expense::factory()->create([
            'state' => Requested::$name,
        ]);
        $expense->state = new Requested($expense);

        $response = $this->service->tryApproveExpense($user, $expense, 'manual');

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

        $response = $this->service->tryRejectExpense($user, $expense, 'reason');

        Event::assertDispatched(ExpenseRejected::class);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_tryRejectExpense_denies_supervisor_with_wrong_state()
    {
        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $expense = Expense::factory()->create([
            'state' => VerifiedBySupervisor::$name,
        ]);
        $expense->state = new VerifiedBySupervisor($expense);

        $response = $this->service->tryRejectExpense($user, $expense, 'reason');

        $this->assertEquals(403, $response->getStatusCode());
    }
   /* public function test_bulk_approve_expenses()
    {
        $user = User::factory()->create();
        $user->assignRole('owner');
        $this->actingAs($user);
        $expenses = Expense::factory()->count(3)->create([
            'state' => Requested::$name,
        ]);

        $ids = $expenses->pluck('id')->toArray();
        $response = $this->postJson(route('expenses.bulk.approve'), [
            'ids' => $ids,
            'payment_method' => 'manual',
        ]);

        $response->assertStatus(200);

        $data = $response->json('results');

        foreach ($ids as $id) {
            $this->assertArrayHasKey($id, $data);
            $this->assertEquals('expense approved', $data[$id]['message']);
        }
    }

    public function test_bulk_reject_expenses()
    {
        $user = User::factory()->create();
        $user->assignRole('supervisor');

        $this->actingAs($user);

        $expenses = Expense::factory()->count(2)->create([
            'state' => Requested::$name,
        ]);

        $ids = $expenses->pluck('id')->toArray();

        $response = $this->postJson(route('expenses.bulk.reject'), [
            'ids' => $ids,
            'rejection_comment' => 'Invalid expense',
        ]);

        $response->assertStatus(200);

        $data = $response->json('results');

        foreach ($ids as $id) {
            $this->assertArrayHasKey($id, $data);
            $this->assertEquals('expense rejected', $data[$id]['message']);
        }
    }*/
}
