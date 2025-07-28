<?php

namespace App\Services\Bank\Payment;

use App\Models\ExpensePaymentLog;
use App\Services\Bank\Contracts\BankInterface;
use App\Services\Bank\Payment\Contracts\PaymentMethodInterface;
use App\States\Payment\VerifiedBySupervisor;

class ManualPayment implements PaymentMethodInterface
{

    public function pay(BankInterface $bank , string $iban , string $amount , string $expenseId)
    {
        try {
            $bank->pay($amount , 'manual callback' , $expenseId);
            $payment = ExpensePaymentLog::query()->updateOrCreate(
                ['expense_id' => $expenseId],
                ['bank_id' => $bank->getId(),
                'method' => 'manual',
                'status' => 'paid',
                'exception_type' => null,
                ]);
        } catch (\Throwable $e) {
            ExpensePaymentLog::query()->updateOrCreate(
                ['expense_id' => $expenseId],
                ['bank_id' => $bank->getId(),
                'status' => 'failed',
                'method' => 'manual' ,
                'exception_type' => get_class($e),
            ]);
        }
    }

}
