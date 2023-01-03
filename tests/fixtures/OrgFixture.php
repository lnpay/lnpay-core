<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class OrgFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\org\models\Org';
    public $depends = [];
    public $dataFile = '@root/tests/_data/org.php';
}