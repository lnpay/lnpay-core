<?php

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__).'/src/',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'lnpay\\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@lnpay/commands' => '@app/commands',
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
            'baseUrl'=>getenv('BASE_URL')
        ],
    ],
];


return $config;