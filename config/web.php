<?php

$config = [
    'id' => 'basic',
    'bootstrap' => [
        'log',
        'monitor',
        'node',
        'wallet',
        'org'
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'defaultRoute'=>'home',
    'modules'=>[
        'monitor' => [
            'class' => \zhuravljov\yii\queue\monitor\Module::class,
            'canPushAgain'=>true,
            'canWorkerStop'=>true,
            'canExecStop'=>true
        ],
        'node' => [
            'class' => lnpay\node\Module::class
        ],
        'wallet' => [
            'class' => lnpay\wallet\Module::class
        ],
        'org' => [
            'class' => lnpay\org\Module::class
        ],
    ],
    'layoutPath' => '@app/views/layouts/sb-admin',
    'layout' => 'main.php',
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    //'jsOptions' => [ 'position' => \yii\web\View::POS_HEAD ],
                ],
            ],
            'appendTimestamp' => true
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
        'user' => [
            'identityClass' => 'lnpay\models\User',
            'enableAutoLogin' => true,
            'loginUrl'=>['/home/login'],
            'as mfa' => [
                'class' => 'vxm\mfa\Behavior',
                'verifyUrl' => '/home/mfa-verify', // verify action, see bellow for setup it
                'enable'=>false
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'home/error',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'lnpay\components\ApiLogTarget',
                ],
            ],
        ],
        'urlManager' => [
            'class'=>'lnpay\components\LNPayUrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/action-name'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/status-type'],

                'POST v1/webhook/subscription/<serviceId:\w+>' => 'v1/webhook/subscribe',
                'DELETE v1/webhook/subscription/<serviceId:\w+>' => 'v1/webhook/unsubscribe',

                'GET,OPTIONS v1/lntx/<id:\w+>' => 'v1/lntx/view',

                //JOBS
                'GET v1/job/<id:\w+>' => 'v1/job/view',

                '/qr' => 'distro-router/qr',
                'distro-router/lnurl-withdraw' => 'distro-router/lnurl-withdraw',


                'developers/dashboard' => 'dashboard/developers',
                'developers/webhook' => 'webhook',
                'developers/api-log' => 'api-log',
                'developers/events' => 'dashboard/events',
                'developers/domain' => 'domain',
            ],
        ],
        'request' => [
            'class'=>'lnpay\components\LNPayRequestComponent',
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => getenv('YII_COOKIE_VALIDATION_KEY'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        //'main' => 'main.php',
                    ],
                ],
            ],
        ],
    ],
];


return $config;
