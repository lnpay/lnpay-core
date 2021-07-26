<?php

// test database! Important not to run tests on production or development databases
$db['dsn'] = 'mysql:host='.getenv('DB_HOST').';dbname=yii2_basic_tests';
$db['username'] = getenv('DB_USER');
$db['password'] = getenv('DB_PASS');
$db['class'] = 'yii\db\Connection';
$db['charset'] = 'utf8';

return $db;
