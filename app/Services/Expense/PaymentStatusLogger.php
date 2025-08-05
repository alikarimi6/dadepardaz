<?php

namespace App\Services\Expense;

use App\Models\ExpenseStatusTransition;

class PaymentStatusLogger
{
    public static function log( $expenseId, $toStatus, $fromStatus , $role , $comment =  null) : void {
        ExpenseStatusTransition::create([
            'expense_id' => $expenseId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'user_id' => auth()->id(),
            'role' => $role ?? auth()->user()?->getRoleNames()->first(),
            'transitioned_at' => now(),
            'comment' => $comment
        ]);
    }
}
