<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class WalletTransactionFixture extends ActiveFixture
{
    public $modelClass = 'app\models\wallet\WalletTransaction';
    public $depends = ['app\tests\fixtures\WalletFixture','app\tests\fixtures\LnTxFixture','app\tests\fixtures\UserFixture'];
    public $dataFile = '@app/tests/_data/wallet_transaction.php';
}