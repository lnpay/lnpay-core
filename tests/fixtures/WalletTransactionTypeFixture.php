<?php

namespace lnpay\fixtures;

use yii\test\ActiveFixture;

class WalletTransactionTypeFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\models\wallet\WalletTransactionType';
    public $depends = [];
    public $dataFile = '@root/tests/_data/wallet_transaction_type.php';
}