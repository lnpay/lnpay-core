<?php

$config = [
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
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
                [
                    'class' => Rekurzia\Log\PapertrailTarget::class,
                    'enabled'=>(bool) (getenv('PAPERTRAIL_HOST') && getenv('PAPERTRAIL_HOST')),
                    'host' => getenv('PAPERTRAIL_HOST'),
                    'port' => getenv('PAPERTRAIL_PORT'),
                    'additionalPrefix' => function() {
                        return getenv('INSTANCE_ID');
                    },
                    'levels' => ['error','warning','info'],
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
            ]
        ]
    ]
];

return $config;