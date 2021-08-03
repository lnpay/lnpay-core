<?php

namespace lnpay\wallet\controllers\api\v1;

use lnpay\base\ApiController;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransactionSearch;
use yii\web\UnauthorizedHttpException;

class WalletTransactionController extends ApiController
{
    /**
     * @var array restrict the following endpoints to sak only
     */
    public $sakOnlyArray = [
        'api/v1/wallet-transaction/view-all',
    ];

    public $modelClass = 'lnpay\wallet\modelsTransaction';

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
