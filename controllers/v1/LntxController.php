<?php

namespace app\controllers\v1;

use app\components\HelperComponent;
use app\models\v1\V1Layout;
use yii\filters\auth\QueryParamAuth;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class LntxController extends BaseApiController
{
    public $modelClass = 'app\models\LnTx';

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
        if ($lntx = $modelClass::find()->where(['external_hash'=>$id,'user_id'=>Yii::$app->user->id])->one()) {
            return $lntx;
        } else {
            throw new UnauthorizedHttpException('LnTx not found');
        }
    }

}
