<?php

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__).'/src/',
    'bootstrap' => [
        'log',
        'monitor',
        'node',
        'wallet'
    ],
    'aliases'=> [
        '@root'=> dirname(__DIR__),
        '@app'=> dirname(__DIR__).'/src/',
        '@app/node'=> dirname(__DIR__).'/src/node/',
        '@app/org'=> dirname(__DIR__).'/src/org/',
        '@vendor'=> dirname(__DIR__).'/vendor',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerNamespace' => 'lnpay\\controllers',
    'runtimePath' => dirname(__FILE__) . '/../runtime',
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
    ],
    'controllerMap'=>[
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
                'zhuravljov\yii\queue\monitor\migrations',
            ],
        ],
    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    //'jsOptions' => [ 'position' => \yii\web\View::POS_HEAD ],
                ],
            ],
            'appendTimestamp' => true
        ],
        'queue' => [
            'class' => \yii\queue\sync\Queue::class,
            'handle'=>true,
            //'as log' => \yii\queue\LogBehavior::class,
            'as jobMonitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class
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
            'class' => 'yii\mutex\MysqlMutex',
            'autoRelease'=>false
        ],
        'user' => [
            'identityClass' => 'lnpay\models\User',
            'enableAutoLogin' => true,
            'loginUrl'=>['/home/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'home/error',
        ],
        'urlManager' => [
            'class'=>'lnpay\components\LNPayUrlManager',
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
                    'class' => 'lnpay\components\ApiLogTarget',
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
