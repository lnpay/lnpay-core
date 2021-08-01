<?php

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'lnpay\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'controllerMap'=>[
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
                'zhuravljov\yii\queue\monitor\migrations',
            ],
        ],
        'monitor' => [
            'class' => \zhuravljov\yii\queue\monitor\console\GcController::class,
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'class'=>'lnpay\components\LNPayUrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error','info','warning'],
                    'logVars' => [],
                    'logTable'=>'log_console',
                    'except' => [
                        'yii\db\Command:*',
                        'yii\db\Connection:*',
                        //'yii\web\Session',
                    ]
                ],
            ],
        ],
    ],
];


return $config;