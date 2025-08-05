<?php

    namespace App\States;
    use App\States\StateStatus;
    use Spatie\ModelStates\State;

    class RejectedByEdari extends StateStatus
    {
        public static  $name = "rejected_by_edari";
    }