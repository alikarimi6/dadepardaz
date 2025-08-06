<?php

namespace App\Listeners;

use App\Events\ExpenseApproved;
use App\Services\Bank\PaymentService;
use App\States\Paid;

class PayExpenseListener
{
    /**
     * Create the event listener.
     */
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function handle(ExpenseApproved $event): void
    {
        $this->paymentService->pay($event->paymentMethod,$event->expense->iban, $event->expense->amount ,$event->expense->id);
        $event->expense->state->transitionTo(Paid::class);
    }
}
