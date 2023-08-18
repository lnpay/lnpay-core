<?php

require(__DIR__ . '/../vendor/autoload.php');

(Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../'))->safeLoad();

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG'));
defined('YII_ENV') or define('YII_ENV',getenv('YII_ENV'));
if (getenv('LNPAY_FLAVOR')=='CLOUD')
    define('LNPAY_FLAVOR_CLOUD',TRUE);
else
    define('LNPAY_FLAVOR_CLOUD',FALSE);

require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../src/LNPay.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php'),
    require(__DIR__ . '/../config/web.php'),
    require(__DIR__ . '/../config/web-local.php')
);

(new yii\web\Application($config))->run();
