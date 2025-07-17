<?php

namespace App\Services\Bank\Payment;

use App\Jobs\ScheduledBankPayment;
use App\Models\ExpensePaymentLog;
use App\Services\Bank\BankInterface;

class ScheduledPayment implements PaymentMethodInterface
{

    public function pay(BankInterface $bank , string $iban , string $amount , string $expenseId)
    {
        $scheduleTime = now()->addMinutes(1);
        try {
            ScheduledBankPayment::dispatch($bank, $iban, $amount , $expenseId)
                ->delay($scheduleTime);
            ExpensePaymentLog::create([
                'bank_id' => $bank->getId(),
                'expense_id' => $expenseId,
                'method' => 'scheduled',
                'scheduled_at' => $scheduleTime,
                'status' => 'paid',
                'exception_type' => null,
            ]);
        } catch (\Throwable $e) {
            ExpensePaymentLog::create([
                'bank_id' => $bank->getId(),
                'expense_id' => $expenseId,
                'status' => 'failed',
                'method' => 'scheduled' ,
                'exception_type' => get_class($e),
            ]);
        }
    }

}
