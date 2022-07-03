<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class CustyDomainFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\models\CustyDomain';
    public $depends = [];
    public $dataFile = '@root/tests/_data/custy_domain.php';
}