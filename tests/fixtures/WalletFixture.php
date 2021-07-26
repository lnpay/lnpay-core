<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class WalletFixture extends ActiveFixture
{
    public $modelClass = 'app\models\wallet\Wallet';
    public $depends = [];
    public $dataFile = '@app/tests/_data/wallet.php';
}