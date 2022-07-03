<?php

$config = [
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info','trace','error','warning'],
                    'logVars' => [],
                    'logTable'=>'log',
                    'except' => [
                        'yii\db\*',
                        'yii\web\*',
                        'yii\base\*',
                        'yii\debug\*'
                    ]
                ],
            ],
        ],
    ],
];

// configuration adjustments for 'dev' environment
$config['bootstrap'][] = 'debug';
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    // uncomment the following to add your IP if you are not connecting from localhost.
    'allowedIPs' => ['127.0.0.1', '::1','192.168.69.1'],
];

$config['bootstrap'][] = 'gii';
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    // uncomment the following to add your IP if you are not connecting from localhost.
    'allowedIPs' => ['127.0.0.1', '::1','192.168.69.1'],
];

return $config;