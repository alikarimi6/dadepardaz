<?php

namespace App\Services\Expense;
use App\Models\Expense;
use App\Models\User;
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
}
