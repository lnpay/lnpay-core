<?php

namespace lnpay\controllers\v1;

use lnpay\base\ApiController;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class LntxController extends ApiController
{
    public $modelClass = 'lnpay\models\LnTx';

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
            'view' =>   ['GET','OPTIONS'],
            //'index'=>   ['GET'],
        ];
    }

    /**
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $modelClass = $this->modelClass;
        if ($lntx = $modelClass::find()->where(['external_hash'=>$id,'user_id'=>\LNPay::$app->user->id])->one()) {
            return $lntx;
        } else {
            throw new UnauthorizedHttpException('LnTx not found');
        }
    }

}
