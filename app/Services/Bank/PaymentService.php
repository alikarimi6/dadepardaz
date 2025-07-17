<?php

namespace App\Services\Bank;

use App\Services\Bank\Payment\ManualPayment;
use App\Services\Bank\Payment\PaymentMethodInterface;
use App\Services\Bank\Payment\ScheduledPayment;

class PaymentService
{
    protected $resolver;

    public function __construct(BankResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function pay(string $paymentMethod ,string $iban, float $amount , string $expenseId)
    {
        $method = $this->resolvePaymentMethod($paymentMethod);
        $bank = $this->resolver->resolve($iban);
        return $method->pay( $bank , $iban , $amount , $expenseId);
    }
    private function resolvePaymentMethod(string $method): PaymentMethodInterface
    {
        return match ($method) {
            'manual'    => new ManualPayment(),
            'scheduled' => new ScheduledPayment(),
        };
    }
}
