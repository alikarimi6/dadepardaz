<?php

namespace App\Services\Bank\Payment;

use App\Models\ExpensePaymentLog;
use App\Services\Bank\Contracts\BankInterface;
use App\Services\Bank\Payment\Contracts\PaymentMethodInterface;

class ManualPayment implements PaymentMethodInterface
{

    public function pay(BankInterface $bank , string $iban , string $amount , string $expenseId)
    {
        try {
            $bank->pay($amount , 'manual callback' , $expenseId);
            ExpensePaymentLog::create([
                'bank_id' => $bank->getId(),
                'expense_id' => $expenseId,
                'method' => 'manual',
                'status' => 'paid',
                'exception_type' => null,
            ]);

        } catch (\Throwable $e) {
            ExpensePaymentLog::create([
                'bank_id' => $bank->getId(),
                'expense_id' => $expenseId,
                'status' => 'failed',
                'method' => 'manual' ,
                'exception_type' => get_class($e),
            ]);
        }
    }

}
