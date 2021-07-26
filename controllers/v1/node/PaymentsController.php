<?php

namespace app\controllers\v1\node;

use app\behaviors\UserAccessKeyBehavior;
use app\modules\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class PaymentsController extends BaseNodeController
{
    public $modelClass = 'app\models\LnTx';

    public function actionDecodeinvoice($payment_request)
    {
        try {
            $node = LnNode::getLnpayNodeQuery()->one();
            return $node->getLndConnector()->decodeInvoice($payment_request);
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }

    public function actionQueryroutes($pub_key,$amt)
    {
        //$this->checkKeyAccess(UserAccessKeyBehavior::PERM_DEFAULT_NODE_WRAPPER_ACCESS);
        try {
            $node = LnNode::getLnpayNodeQuery()->one();
            return $node->getLndConnector()->queryRoutes(compact('pub_key','amt'));
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }

}
