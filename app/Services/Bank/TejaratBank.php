<?php

namespace App\Services\Bank;

use App\Services\Bank\BankInterface;

class TejaratBank implements BankInterface
{
    private string $prefix  = '33';
    private string $id  = '3';

    public function pay($amount, $callback , string $expenseId)
    {
        logger("tejarat bank paid $amount , callback: $callback");

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
