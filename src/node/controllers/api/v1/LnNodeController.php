<?php

namespace lnpay\node\controllers\api\v1;

use lnpay\base\ApiController;
use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\HelperComponent;
use lnpay\jobs\LnWalletKeysendFormJob;
use lnpay\models\LnTx;
use lnpay\models\User;
use lnpay\wallet\models\LnWalletKeysendForm;
use lnpay\wallet\models\WalletTransaction;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransactionSearch;
use lnpay\wallet\models\WalletTransferForm;

use lnpay\node\models\LnNode;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class LnNodeController extends ApiController
{
    /**
     * @var array restrict the following endpoints to sak only
     */
    public $sakOnlyArray = [
        'api/v1/ln-node/view-all',
        'api/v1/ln-node/view',
    ];

    public $modelClass = 'lnpay\node\models\LnNode';

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
            'create' => ['POST','OPTIONS'],
            'update' => ['PUT','PATCH','POST','OPTIONS'],
            'delete' => ['DELETE','OPTIONS'],
            'view' =>   ['GET','OPTIONS'],
            'index'=>   ['GET','OPTIONS'],
            'view-all'=>['GET','OPTIONS']
        ];
    }

    public function findByKey($node_id)
    {
        return LnNode::find()->where(['id'=>$node_id,'user_id'=>\LNPay::$app->user->id])->one();
    }

    public function actionCreate()
    {
        throw new ServerErrorHttpException('Endpoint not active');
    }

    /**
     *
     * @param $id
     * @return LnNode
     * @throws NotFoundHttpException
     */
    public function actionView($node_id)
    {
        return $this->findByKey($node_id);
    }


    public function actionViewAll()
    {
        $modelClass = $this->modelClass;
        return new \yii\data\ActiveDataProvider([
            'query' => $modelClass::find()->where(['user_id'=>\LNPay::$app->user->id]),
            'pagination' => [
                'defaultPageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);
    }


}
