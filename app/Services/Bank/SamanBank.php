<?php

namespace App\Services\Bank;

use App\Services\Bank\Contracts\BankInterface;

class SamanBank implements BankInterface
{
    private string $prefix  = '22';
    private string $id  = '2';
    public function pay($amount, $callback , string $expenseId)
    {
        logger("saman bank paid $amount , callback: $callback");
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
