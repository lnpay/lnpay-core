<?php
define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require(__DIR__ . '/../src/LNPay.php');
require __DIR__ .'/../vendor/autoload.php';

(Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../'))->safeLoad();

shell_exec("mysql -h".getenv('DB_HOST')." -u".getenv('DB_USER')." -p".getenv('DB_PASS')." -e \"DROP DATABASE IF EXISTS yii2_basic_tests\"");
shell_exec("mysql -h".getenv('DB_HOST')." -u".getenv('DB_USER')." -p".getenv('DB_PASS')." -e \"CREATE DATABASE IF NOT EXISTS yii2_basic_tests;\"");
shell_exec("php tests/bin/yii migrate --migrationPath=@yii/rbac/migrations --interactive=0");
shell_exec("php tests/bin/yii migrate --migrationPath=@app/migrations --interactive=0");

