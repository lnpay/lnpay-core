<?php

namespace lnpay\node\controllers\api\v1;

use lnpay\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;

class LncliApiController extends NodeApiController
{
    public $modelClass = 'lnpay\node\models\LnNode';

    public function actionLookupinvoice($r_hash_str,$node_id)
    {
        try {
            return $this->nodeObject->getLndConnector()->lookupInvoice($r_hash_str);
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }
}
