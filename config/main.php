<?php

$params = require(__DIR__ . '/params.php');

return [
    'name'=>'LNPAY',
    'bootstrap' => [
        'queue', // The component registers its own console
    ],
    'aliases'=> [
        '@root'=> dirname(__DIR__),
        '@app'=> dirname(__DIR__).'/src/',
        '@lnpay'=> dirname(__DIR__).'/src/',
        '@app/node'=> dirname(__DIR__).'/src/node/',
        '@app/wallet'=> dirname(__DIR__).'/src/wallet/',
        '@vendor'=> dirname(__DIR__).'/vendor',
    ],
    'basePath' => dirname(__DIR__).'/src/',
    'controllerNamespace' => 'lnpay\\controllers',
    'runtimePath' => dirname(__FILE__) . '/../runtime',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DB'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => \yii\caching\DbCache::class,
        ],
        'mutex' => [
            'class' => 'yii\mutex\MysqlMutex',
        ],
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            //'as log' => \yii\queue\LogBehavior::class,
            'as jobMonitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class,
            'db' => 'db', // DB connection component or its config
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error','warning','info'],
                    'logTable'=>'log',
                    'except' => [

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
                ]
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
    'params'=>$params
];
