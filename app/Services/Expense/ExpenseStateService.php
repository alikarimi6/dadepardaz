<?php

namespace App\Services\Expense;
use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use App\Http\Resources\Api\V1\ExpenseResource;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\States\Payment\{
    Requested,
    VerifiedBySupervisor,
    RejectedBySupervisor,
    VerifiedByOwner,
    RejectedByOwner,
    Paid,
    PayError
};
class ExpenseStateService
{
    protected array $transitions = [
        'supervisor' => [
            'approved' => [
                Requested::class => VerifiedBySupervisor::class,
            ],
            'rejected' => [
                Requested::class => RejectedBySupervisor::class,
            ],
        ],
        'owner' => [
            'approved' => [
                VerifiedBySupervisor::class => VerifiedByOwner::class,
                VerifiedByOwner::class => Paid::class,
            ],
            'rejected' => [
                VerifiedBySupervisor::class => RejectedByOwner::class,
                VerifiedByOwner::class => PayError::class,
            ],
        ],
    ];

    public function transition(Expense $expense, User $performedBy, string $action, ?string $comment = null)
    {
        $role = $performedBy->getRoleNames()->first();
        $currentState = get_class($expense->state);

        if (! isset($this->transitions[$role][$action][$currentState])) {
            return ;
        }

        $nextState = $this->transitions[$role][$action][$currentState];

        $expense->state->transitionTo($nextState);

        $expense->update([
            'rejection_comment' => $action === 'rejected' ? $comment : null,
        ]);

        return true;
    }
    public function tryApproveExpense($user, Expense $expense, $paymentMethod): JsonResponse
    {
        $canApprove = (
            ($user->hasRole('owner') && $expense->state == VerifiedBySupervisor::$name) ||
            ($user->hasRole('supervisor') && $expense->state == Requested::$name)
        );

        if ($canApprove) {
            event(new ExpenseApproved(
                $expense->user()->first(),
                $expense,
                $paymentMethod
            ));

            return response()->json([
                'message' => 'expense approved',
                'expense' => ExpenseResource::make($expense),
            ]);
        }

        return response()->json(['message' => 'no access' ,
            'state' => $expense->state ,
            'role' => $user->hasRole('owner'),
        ] , 403);
    }
    public function tryRejectExpense($user, Expense $expense, $rejectionComment): JsonResponse
    {
        $canReject = (
            ($user->hasRole('owner') && $expense->state == VerifiedBySupervisor::$name) ||
            ($user->hasRole('supervisor') && $expense->state == Requested::$name)
        );

        if ($canReject) {
            event(new ExpenseRejected(
                $expense->user()->first(),
                $expense,
                $rejectionComment
            ));
            return response()->json([
                'message' => 'expense rejected',
                'expense' => ExpenseResource::make($expense),
            ]);
        }

        return response()->json(['message' => 'no access' ,
        'user' => auth()->user() ,
        'role' => $user->hasRole('supervisor'),
        ] , 403);
    }


}
