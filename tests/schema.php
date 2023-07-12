<?php
declare(strict_types=1);

return [
    [
        'table' => 'users',
        'columns' => [
            'id' => [
                'type' => 'integer',
            ],
            'username' => [
                'type' => 'string',
                'null' => true,
            ],
            'email' => [
                'type' => 'string',
                'null' => true,
            ],
            'password' => [
                'type' => 'string',
                'null' => true,
            ],
            'first_name' => [
                'type' => 'string',
                'null' => true,
            ],
            'last_name' => [
                'type' => 'string',
                'null' => true,
            ],
            'active' => [
                'type' => 'boolean',
            ],
            'created' => [
                'type' => 'timestamp',
                'null' => true,
            ],
            'updated' => [
                'type' => 'timestamp',
                'null' => true,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => [
                    'id',
                ],
            ],
        ],
    ],
];
