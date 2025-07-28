<?php

namespace App\Services\Bank;

use App\Services\Bank\Contracts\BankInterface;

class BankResolver {
    /** @var BankInterface[] */
    private array $banks;

    public function __construct() {
        $this->banks = [new SarmayeBank() , new SamanBank() , new TejaratBank()];
    }

    public function resolve(string $iban): BankInterface {
        $prefix = substr($iban, 0, 2);

        foreach ($this->banks as $bank) {
            if ($bank->getPrefix() === $prefix) {
                return $bank;
            }
        }

        throw new \Exception("Bank not found for prefix {$prefix}");
    }
}
