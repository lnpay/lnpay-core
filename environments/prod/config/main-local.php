<?php

return [
    'components' => [
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error','warning','info'],
                    'logTable'=>'log',
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\rbac\DbManager:*',
                        'yii\db\*',
                        'yii\web\Session::*',
                        'yii\filters\RateLimiter::*',
                        'yii\web\User::login',
                        'yii\web\User::loginByCookie'
                    ],
                    'maskVars'=>[
                        '_SERVER.DEFAULT_EMAIL_USERNAME',
                        '_SERVER.DEFAULT_EMAIL_PASSWORD',
                        '_SERVER.AMPLITUDE_API_KEY',
                        '_SERVER.DB_USER',
                        '_SERVER.DB_PASS',
                        '_SERVER.DB_HOST',
                        '_SERVER.DB_DB',
                    ]
                ],
            ],
        ],
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
            'autoRelease'=>false,
            'expire'=>3600,
            'redis' => [
                'hostname' => getenv('REDIS_HOST'),
                'port' => 6379,
                'database' => getenv('REDIS_MUTEX_DB'),
            ]
        ]
    ]
];
