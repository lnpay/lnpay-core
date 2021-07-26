<?php

namespace app\controllers\v1\node;

use app\modules\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;

class LncliController extends BaseNodeController
{
    public $modelClass = 'app\modules\node\models\LnNode';

    public function actionLookupinvoice($r_hash_str,$node_id)
    {
        try {
            return $this->nodeObject->getLndConnector()->lookupInvoice($r_hash_str);
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }
}
