<?php

namespace App\Services\Bank\Payment;

use App\Jobs\ScheduledBankPayment;
use App\Models\ExpensePaymentLog;
use App\Services\Bank\Contracts\BankInterface;
use App\Services\Bank\Payment\Contracts\PaymentMethodInterface;
use Illuminate\Support\Facades\Config;

class ScheduledPayment implements PaymentMethodInterface
{

    public function pay(BankInterface $bank , string $iban , string $amount , string $expenseId)
    {
        $scheduleTime = now()->addMinutes(Config::get('payment.schedule_time'));
        try {
            ExpensePaymentLog::query()->updateOrCreate(
                ['expense_id' => $expenseId,] ,
                ['bank_id' => $bank->getId(),
                'method' => 'scheduled',
                'scheduled_at' => $scheduleTime,
                'status' => 'paid',
                'exception_type' => null,
            ]);
        } catch (\Throwable $e) {

            ExpensePaymentLog::query()->updateOrCreate(
                ['expense_id' => $expenseId,]
                ,['bank_id' => $bank->getId(),
                'status' => 'failed',
                'method' => 'scheduled' ,
                'exception_type' => get_class($e),
            ]);
        }
    }

}
