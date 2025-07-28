<?php

namespace App\Services\Bank\Contracts;

interface BankInterface
{
    public function pay($amount , $callback , string $expenseId);
    public function getPrefix(): string;
    public function getId() : string;

}
