<?php

namespace lnpay\node\controllers\api\v1;

use lnpay\base\ApiController;
use lnpay\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class NodeApiController extends ApiController
{
    public $nodeObject;

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['index']);
        return $actions;
    }

    protected function verbs(){
        return [
            //'create' => ['POST'],
            //'update' => ['PUT','PATCH','POST'],
            //'delete' => ['DELETE'],
            //'view' =>   ['GET','OPTIONS'],
            //'index'=>   ['GET'],
        ];
    }

    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {
            if ($node_id = \LNPay::$app->request->getQueryParam('node_id')) {
                if ($node_id == 'default')
                    $this->nodeObject = $this->user->getLnNodeQuery()->one();
                else
                    $this->nodeObject = $this->user->getLnNodeQuery()->andWhere(['id'=>$node_id])->one();

                if (!$this->nodeObject) {
                    throw new UnauthorizedHttpException('Invalid node id: '.$node_id);
                }

            } else {
                throw new UnauthorizedHttpException('Request must contain node_id');
            }
        }

        return true;
    }
}
