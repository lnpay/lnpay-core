<?php

namespace app\modules\node\fixtures;

use yii\test\ActiveFixture;

class LnNodeImplementationFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\node\models\LnNodeImplementation';
    public $depends = [];
    public $dataFile = '@app/modules/node/fixtures/_data/ln_node_implementation.php';
}