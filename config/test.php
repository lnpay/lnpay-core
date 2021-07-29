<?php

$config = [
    'name'=>'Paywall.Link',
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','queue'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'defaultRoute'=>'home',
    'controllerMap'=>[
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
                'zhuravljov\yii\queue\monitor\migrations',
            ],
        ],
    ],
    'modules'=>[
        'monitor' => [
            'class' => \zhuravljov\yii\queue\monitor\Module::class,
            'canPushAgain'=>true,
            'canWorkerStop'=>true,
            'canExecStop'=>true
        ],
    ],
    'components' => [
        'queue' => [
            'class' => \yii\queue\sync\Queue::class,
            'handle'=>true,
            //'as log' => \yii\queue\LogBehavior::class,
            'as jobMonitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class
        ],
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
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mutex' => [
            'class' => 'yii\mutex\FileMutex'
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl'=>['/home/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'home/error',
        ],
        'urlManager' => [
            'class'=>'app\components\LNPayUrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => true,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/action-name'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'v1/status-type'],

                'POST v1/webhook/subscription/<serviceId:\w+>' => 'v1/webhook/subscribe',
                'DELETE v1/webhook/subscription/<serviceId:\w+>' => 'v1/webhook/unsubscribe',

                //WALLETS NEW NEW
                'GET,HEAD,OPTIONS v1/wallets' => 'v1/wallet/view-all',
                'GET,HEAD,OPTIONS v1/wallet/<access_key:\w+>' => 'v1/wallet/view',
                'GET,OPTIONS v1/wallet/<access_key:\w+>/lnurl/withdraw-static' => 'v1/wallet/lnurl-withdraw-static',
                'GET,OPTIONS v1/wallet/<access_key:\w+>/lnurl/withdraw' => 'v1/wallet/lnurl-withdraw',
                'GET,OPTIONS v1/wallet/<access_key:\w+>/lnurl-process' => 'v1/wallet/lnurl-process',
                'POST,OPTIONS v1/wallet' => 'v1/wallet/create',
                'POST,OPTIONS v1/wallet/<access_key:\w+>/withdraw' => 'v1/wallet/withdraw',
                'POST,OPTIONS v1/wallet/<access_key:\w+>/keysend' => 'v1/wallet/keysend',
                'POST,OPTIONS v1/wallet/<access_key:\w+>/invoice' => 'v1/wallet/invoice',
                'POST,OPTIONS v1/wallet/<access_key:\w+>/transfer' => 'v1/wallet/transfer',
                'GET,OPTIONS v1/wallet/<access_key:\w+>/transactions' => 'v1/wallet/transactions',
                'GET,OPTIONS v1/lntx/<id:\w+>' => 'v1/lntx/view',

                //WALLET-TRANSACTIONS
                'GET,OPTIONS v1/wallet-transactions' => 'v1/wallet-transaction/view-all',

                //NODE
                'GET,HEAD,OPTIONS v1/node/<node_id:\w+>/<controller:\w+>/<action:\w+>' => 'v1/node/<controller>/<action>',

                //JOBS
                'GET v1/job/<id:\w+>' => 'v1/job/view',

                '/qr' => 'distro-router/qr',
                'distro-router/lnurl-withdraw' => 'distro-router/lnurl-withdraw',


                'developers/dashboard' => 'dashboard/developers',
                'developers/webhook' => 'webhook',
                'developers/api-log' => 'api-log',
                'developers/events' => 'dashboard/events',

                //MODULES!
                '<module:\w+>/<controller:[A-Za-z0-9 -_.]+>/<action:\w+>/<id:[A-Za-z0-9_]+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:[A-Za-z0-9 -_.]+>/<action:\w+>' => '<module>/<controller>/<action>',


            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'lhz6GXi4DgVEa4MTjoJpUYvZScpznBu1',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'db'=>require(__DIR__ . '/test_db.php'),
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info','trace','error','warning'],
                    'logVars' => [],
                    'logTable'=>'log',
                    'except' => [
                        'yii\db\*',
                        'yii\web\Session::*',
                        'yii\base\*',
                        'yii\web\*'
                    ]
                ],
                [
                    'class' => 'app\components\ApiLogTarget',
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => getenv('DEFAULT_EMAIL_HOST'),
                'username' => getenv('DEFAULT_EMAIL_USERNAME'),
                'password' => getenv('DEFAULT_EMAIL_PASSWORD'),
                'port' => getenv('DEFAULT_EMAIL_PORT'),
                'encryption' => 'tls',
            ],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => (in_array(getenv('YII_ENV'),['dev','test'])?true:false),
        ],
    ],
    'params' => require(__DIR__ . '/params.php')
];



return $config;
