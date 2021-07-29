<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class LnTxFixture extends ActiveFixture
{
    public $modelClass = 'app\models\LnTx';
    public $depends = ['app\modules\node\fixtures\LnNodeFixture','app\tests\fixtures\UserFixture'];
    public $dataFile = '@app/tests/_data/ln_tx.php';
}