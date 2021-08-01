<?php

namespace lnpay\fixtures;

use yii\db\ActiveRecord;
use yii\test\ActiveFixture;

class AuthItemFixture extends ActiveFixture
{
    public $depends = [];
    public $dataFile = '@root/tests/_data/auth_item.php';
    public $tableName = 'auth_item';
}