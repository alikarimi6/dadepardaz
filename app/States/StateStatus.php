<?php

namespace App\States;

use App\Models\State as StateModel;
use App\Models\Transition;
use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class StateStatus extends State
{
    public static function config(): StateConfig
    {
        $config = parent::config();
        $config->default(Requested::class);

        $transitions = Transition::with(['fromState', 'toState'])->get();

        foreach ($transitions as $transition) {
            $fromAction = $transition->fromState->action ;
            $toAction = $transition->toState->action;

            try {
                $fromClass = self::mapActionToStateClass($fromAction);
                $toClass = self::mapActionToStateClass($toAction);

                $config->allowTransition($fromClass, $toClass);
            } catch (\InvalidArgumentException|InvalidConfig $e) {
                dd($e->getMessage());
            }
        }

        return $config;
    }
    public static function mapActionToStateClass(string $action): string
    {
        return match ($action) {
            'action' => Requested::class,
            'approve', 'rollback' => Pending::class,
            'reject' => Rejected::class,
            'payment' => PendingPay::class,
            'paid' => Paid::class,
            default => throw new \InvalidArgumentException("Unknown action: $action"),
        };
    }
}
