<?php

namespace app\modules\node\fixtures;

use yii\test\ActiveFixture;

class LnNodeFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\node\models\LnNode';
    public $depends = [
        'app\modules\node\fixtures\LnNodeImplementationFixture',
        'app\modules\node\fixtures\LnNodeProfileFixture',
        'app\modules\node\fixtures\LnNodeDefaultWalletsFixture'];
    public $dataFile = '@app/modules/node/fixtures/_data/ln_node.php';
}