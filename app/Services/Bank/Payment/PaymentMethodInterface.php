<?php

namespace App\Services\Bank\Payment;

use App\Services\Bank\BankInterface;

interface PaymentMethodInterface
{
    public function pay(BankInterface $bank , string $iban , string $amount , string $expenseId);

}
