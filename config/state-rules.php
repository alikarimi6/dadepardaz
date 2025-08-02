<?php
return [
    'owner' => [
        'approved' => \App\States\Payment\VerifiedByOwner::$name,
        'rejected' => \App\States\Payment\RejectedByOwner::$name,
    ] ,
    'supervisor' => [
        'approved' => \App\States\Payment\VerifiedBySupervisor::$name,
        'rejected' => \App\States\Payment\RejectedBySupervisor::$name,
    ]
];
