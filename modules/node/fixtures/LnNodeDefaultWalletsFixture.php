<?php

namespace app\modules\node\fixtures;

use yii\test\ActiveFixture;

class LnNodeDefaultWalletsFixture extends ActiveFixture
{
    public $modelClass = 'app\models\wallet\Wallet';
    public $depends = [];
    public $dataFile = '@app/modules/node/fixtures/_data/ln_node_default_wallets.php';
}