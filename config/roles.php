<?php
return [
    'permissions' => [
        'approve by supervisor',
        'reject by supervisor',
        'approve by owner',
        'reject by owner',
        'view payments',
        'make payment',
    ],

    'roles' => [
        'supervisor' => [
            'approve by supervisor',
            'reject by supervisor',
            'view payments',
        ],
        'owner' => [
            'approve by owner',
            'reject by owner',
            'make payment',
            'view payments',
        ],
    ],

    'users' => [
        2 => 'supervisor',
        1 => 'owner',
    ],
];
