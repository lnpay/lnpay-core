<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class UserAccessKeyFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\models\UserAccessKey';
    public $depends = ['lnpay\fixtures\WalletFixture','lnpay\fixtures\AuthAssignmentFixture'];
    public $dataFile = '@root/tests/_data/user_access_key.php';
}