<?php

    namespace App\States;
    use App\States\StateStatus;
    use Spatie\ModelStates\State;

    class Requested extends StateStatus
    {
        public static  $name = "requested";
    }