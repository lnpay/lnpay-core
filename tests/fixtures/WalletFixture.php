<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class WalletFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\wallet\models\Wallet';
    public $depends = [];
    public $dataFile = '@root/tests/_data/wallet.php';
}