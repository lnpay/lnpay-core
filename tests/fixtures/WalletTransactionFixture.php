<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class WalletTransactionFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\wallet\models\WalletTransaction';
    public $depends = ['lnpay\fixtures\WalletFixture','lnpay\fixtures\LnTxFixture','lnpay\fixtures\UserFixture'];
    public $dataFile = '@root/tests/_data/wallet_transaction.php';
}