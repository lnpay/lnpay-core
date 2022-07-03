<?php

namespace lnpay\node\fixtures;

use yii\test\ActiveFixture;

class LnNodeProfileFixture extends ActiveFixture
{
    public $modelClass = 'lnpay\node\models\LnNodeProfile';
    public $depends = ['lnpay\node\fixtures\LnNodeFixture'];
    public $dataFile = '@app/node/fixtures/_data/ln_node_profile.php';
}