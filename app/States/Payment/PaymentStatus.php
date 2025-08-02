<?php

namespace App\States\Payment;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class PaymentStatus extends State
{

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Requested::class)
            ->allowTransition(Requested::class, VerifiedBySupervisor::class)
            ->allowTransition(Requested::class, RejectedBySupervisor::class)
            ->allowTransition(VerifiedBySupervisor::class, VerifiedByOwner::class)
            ->allowTransition(VerifiedBySupervisor::class, RejectedByOwner::class)
            ->allowTransition(VerifiedByOwner::class, Paid::class)
            ->allowTransition(VerifiedByOwner::class, PayError::class);
    }
}
