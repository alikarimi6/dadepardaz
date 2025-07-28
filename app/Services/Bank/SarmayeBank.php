<?php

namespace App\Services\Bank;

use App\Services\Bank\Contracts\BankInterface;

class SarmayeBank implements BankInterface
{
    private string $prefix  = '11';
    private string $id  = '1';

    public function pay($amount, $callback , string $expenseId)
    {
        logger("sarmaye bank paid $amount , callback: $callback");
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
