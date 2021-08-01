<?php

namespace lnpay\node\fixtures;

use yii\test\ActiveFixture;

class LnNodeImplementationFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\node\models\LnNodeImplementation';
    public $depends = [];
    public $dataFile = '@app/node/fixtures/_data/ln_node_implementation.php';
}