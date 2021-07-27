<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class UserAccessKeyFixture extends ActiveFixture
{
    public $modelClass = 'app\models\UserAccessKey';
    public $depends = ['app\tests\fixtures\WalletFixture','app\tests\fixtures\AuthAssignmentFixture'];
    public $dataFile = '@app/tests/_data/user_access_key.php';
}