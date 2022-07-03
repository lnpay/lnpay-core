<?php

namespace lnpay\controllers\v1;

use lnpay\base\ApiController;

class StatusTypeController extends ApiController
{
    public $modelClass = 'lnpay\models\StatusType';

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);

        unset($actions['index']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    protected function verbs(){
        return [
            //'create' => ['POST'],
            //'update' => ['PUT','PATCH','POST'],
            //'delete' => ['DELETE'],
            'view' =>   ['GET'],
            'index'=>   ['GET'],
        ];
    }


    public function actionIndex($type)
    {
        $modelClass = $this->modelClass;
        return new \yii\data\ActiveDataProvider([
            'query' => $modelClass::find()->where(['type'=>$type]),
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ]
            ],
        ]);
    }



}
