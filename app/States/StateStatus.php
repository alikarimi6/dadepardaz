<?php

namespace App\States;

use App\Models\State as StateModel;
use App\Models\Transition;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class StateStatus extends State
{
    public static function config(): StateConfig
    {
//        todo : default and transition dynamically
//        todo : define rollbacks
        $config = parent::config();
        $defaultState = StateModel::query()->where('is_default', true)->first();
        if ($defaultState) {
            $config->default($defaultState->class);
        }

        $transitions = Transition::with(['fromState', 'toState'])->get();

        foreach ($transitions as $transition) {
            if ($transition->fromState && $transition->toState && class_exists($transition->fromState->class) && class_exists($transition->toState->class)) {
                $config->allowTransition(
                    $transition->fromState->class,
                    $transition->toState->class
                );
            }
        }

        return $config;
    }
}
