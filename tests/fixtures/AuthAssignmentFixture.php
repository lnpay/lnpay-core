<?php

namespace app\tests\fixtures;

use yii\db\ActiveRecord;
use yii\test\ActiveFixture;

class AuthAssignmentFixture extends ActiveFixture
{
    public $depends = ['app\tests\fixtures\AuthItemFixture'];
    public $dataFile = '@app/tests/_data/auth_assignment.php';
    public $tableName = 'auth_assignment';
}