<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class WalletTransactionTypeFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\wallet\models\WalletTransactionType';
    public $depends = [];
    public $dataFile = '@root/tests/_data/wallet_transaction_type.php';
}