<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class LnTxFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\models\LnTx';
    public $depends = ['lnpay\node\fixtures\LnNodeFixture','lnpay\fixtures\UserFixture'];
    public $dataFile = '@root/tests/_data/ln_tx.php';
}