<?php

namespace lnpay\controllers\v1;

use lnpay\components\HelperComponent;
use lnpay\models\wallet\Wallet;
use lnpay\models\wallet\WalletTransactionSearch;
use yii\filters\auth\QueryParamAuth;

use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class WalletTransactionController extends BaseApiController
{
    public $modelClass = 'lnpay\models\WalletTransaction';

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


    public function actionViewAll($wallet_id=NULL)
    {
        $searchModel = new WalletTransactionSearch();
        $searchModel->user_id = \LNPay::$app->user->id;

        if ($wallet_id) {
            $wal = Wallet::findById($wallet_id);
            if (!$wal || ($wal->user_id != \LNPay::$app->user->id)) {
                throw new UnauthorizedHttpException('Wallet not found');
            }
            $searchModel->wallet_id = $wal->id;
        }
        $dataProvider = $searchModel->search(\LNPay::$app->request->queryParams);
        $dataProvider->pagination->defaultPageSize = 20;

        return $dataProvider;
    }



}
