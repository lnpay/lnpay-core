<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class WalletLnurlpayFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\wallet\models\WalletLnurlpay';
    public $depends = ['lnpay\fixtures\WalletFixture'];
    public $dataFile = '@root/tests/_data/wallet_lnurlpay.php';
}