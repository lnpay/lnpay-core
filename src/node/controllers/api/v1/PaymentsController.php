<?php

namespace lnpay\node\controllers\api\v1;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class PaymentsController extends NodeApiController
{
    public $modelClass = 'lnpay\models\LnTx';

    public function actionDecodeinvoice($payment_request)
    {
        try {
            $node = $this->nodeObject;
            return $node->getLndConnector()->decodeInvoice($payment_request);
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }

    public function actionQueryroutes($pub_key,$amt)
    {
        //$this->checkAccessKey(UserAccessKeyBehavior::PERM_DEFAULT_NODE_WRAPPER_ACCESS);
        try {
            $node = $this->nodeObject;
            return $node->getLndConnector()->queryRoutes(compact('pub_key','amt'));
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }

}
