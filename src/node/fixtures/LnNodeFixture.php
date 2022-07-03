<?php

namespace lnpay\node\fixtures;

use yii\test\ActiveFixture;

class LnNodeFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\node\models\LnNode';
    public $depends = [
        'lnpay\node\fixtures\LnNodeImplementationFixture',
        'lnpay\node\fixtures\LnNodeProfileFixture',
        'lnpay\node\fixtures\LnNodeDefaultWalletsFixture'];
    public $dataFile = '@app/node/fixtures/_data/ln_node.php';
}