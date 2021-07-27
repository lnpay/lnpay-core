<?php

namespace app\modules\node\fixtures;

use yii\test\ActiveFixture;

class LnNodeProfileFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\node\models\LnNodeProfile';
    public $depends = ['app\modules\node\fixtures\LnNodeFixture'];
    public $dataFile = '@app/modules/node/fixtures/_data/ln_node_profile.php';
}