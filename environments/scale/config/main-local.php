<?php

return [
    'components' => [
        'cache' => [
            'class' => \yii\redis\Cache::class,
            'redis' => [
                'hostname' => getenv('REDIS_HOST'),
                'port' => 6379,
                'database' => getenv('REDIS_CACHE_DB'),
            ]
        ],
        'mutex' => [
            'class' => 'yii\redis\Mutex',
            'redis' => [
                'hostname' => getenv('REDIS_HOST'),
                'port' => 6379,
                'database' => getenv('REDIS_MUTEX_DB'),
            ]
        ]
    ]
];
