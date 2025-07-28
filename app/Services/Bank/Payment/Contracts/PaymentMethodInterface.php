<?php

namespace App\Services\Bank\Payment\Contracts;

use App\Services\Bank\Contracts\BankInterface;

interface PaymentMethodInterface
{
    public function pay(BankInterface $bank , string $iban , string $amount , string $expenseId);

}
