<?php

use App\Enums\RolesEnum;

return [
    'users' => [
        [
            'email' => 'test@mail.com',
            'password' => 'Password123',
            'profile' => [
                'first_name' => 'Jack',
                'last_name' => 'Rose',
            ],
            'role_id' => RolesEnum::Admin
        ],
    ],
];