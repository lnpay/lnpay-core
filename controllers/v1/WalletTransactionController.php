<?php

namespace app\controllers\v1;

use app\components\HelperComponent;
use app\models\wallet\Wallet;
use app\models\wallet\WalletTransactionSearch;
use yii\filters\auth\QueryParamAuth;

use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class WalletTransactionController extends BaseApiController
{
    public $modelClass = 'app\models\WalletTransaction';

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


    public function actionViewAll($wallet=NULL)
    {
        $searchModel = new WalletTransactionSearch();
        $searchModel->user_id = Yii::$app->user->id;

        if ($wallet) {
            $searchModel->wallet_id = @Wallet::findById($wallet)->id;
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->defaultPageSize = 20;

        return $dataProvider;
    }



}
