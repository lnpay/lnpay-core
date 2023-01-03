<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\models\User';
    public $depends = ['lnpay\fixtures\OrgFixture'];
    public $dataFile = '@root/tests/_data/user.php';
}