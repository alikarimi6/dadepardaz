<?php

namespace App\Services\Bank;

interface BankInterface
{
    public function pay($amount , $callback , string $expenseId);
    public function getPrefix(): string;
    public function getId() : string;

}
