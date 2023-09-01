<?php

namespace lnpay\wallet\controllers\api\v1;

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

class WalletController extends ApiController
{
    /**
     * @var array restrict the following endpoints to sak only
     */
    public $sakOnlyArray = [
        'api/v1/wallet-transaction/view-all',
        'api/v1/wallet/view-all',
    ];

    public $modelClass = 'lnpay\wallet\models\Wallet';

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

    public function actionCreate()
    {
        $model = new Wallet();

        $model->load(\LNPay::$app->getRequest()->getBodyParams(), '');
        $model->user_id = \LNPay::$app->user->id;

        if ($model->deterministic_identifier) {
            $model->external_hash = 'walx_'.HelperComponent::generateDeterministicString(
                $model->deterministic_identifier,
                $salt = \LNPay::$app->user->identity->org->external_hash,
                $length = 14);
        }

        if ($model->save()) {
            $response = \LNPay::$app->getResponse();
            $response->setStatusCode(201);

            $wallet = Wallet::findOne($model->id);
            return ArrayHelper::merge($wallet->toArray(),['access_keys'=>$wallet->userAccessKeys]);

        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($model));
        }
    }

    /**
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($access_key)
    {
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_READ);
        return $this->findByKey($access_key);
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

    public function actionWithdraw($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $model = new LnWalletWithdrawForm();
        $model->load(\LNPay::$app->getRequest()->getBodyParams(), '');
        $model->wallet_id = $wallet->id;
        \LNPay::$app->getRequest()->getBodyParam('passThru',[]);

        $array = [];
        if ($passThru = \LNPay::$app->request->getBodyParam('passThru')) {
            if (is_array($passThru)) {
                $array = $passThru;
            } else {
                try {
                    $array = Json::decode($passThru);
                } catch (\Throwable $t) {
                    throw new BadRequestHttpException('passThru data must be valid json');
                }
            }
        }

        $model->passThru = $array;

        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);

        $wtx = $model->processWithdrawal(['method'=>'api']);
        \LNPay::$app->response->statusCode = 201;
        return WalletTransaction::findOne($wtx->id);
    }

    public function actionKeysend($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $bodyParams = \LNPay::$app->getRequest()->getBodyParams();
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);



        if ($this->isAsync) {
            $id = \LNPay::$app->queue->push(new LnWalletKeysendFormJob([
                'access_key' => $access_key,
                'wallet_id' => $wallet->id,
                'bodyParams'=>$bodyParams
            ]));
            return ['success'=>1,'id'=>$id];
        } else {
            $job = new LnWalletKeysendFormJob([
                'access_key' => $access_key,
                'wallet_id' => $wallet->id,
                'bodyParams'=>$bodyParams
            ]);
            $wtx_id = $job->execute(\LNPay::$app->queue);
        }

        \LNPay::$app->response->statusCode = 201;
        return WalletTransaction::findOne($wtx_id);
    }

    public function actionLnurlWithdraw($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);

        $params = [
            'num_satoshis'=>\LNPay::$app->request->getQueryParam('num_satoshis'),
            'memo'=>\LNPay::$app->request->getQueryParam('memo'),
            'ott'=>HelperComponent::generateRandomString(12),
            'passThru'=>\LNPay::$app->request->getQueryParam('passThru'),
            'public'=>\LNPay::$app->request->getQueryParam('public')
            ];
        $array = [
            'lnurl'=>$wallet->getLnurlWithdrawLinkEncoded($access_key=NULL,$params),
            'ott'=>$params['ott'],
        ];
        return $array;
    }

    public function actionLnurlWithdrawStatic($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::ROLE_WALLET_ADMIN);

        $params = [
            'num_satoshis'=>\LNPay::$app->request->getQueryParam('num_satoshis'),
            'memo'=>\LNPay::$app->request->getQueryParam('memo'),
            'passThru'=>\LNPay::$app->request->getQueryParam('passThru')
        ];
        $array = [
            'lnurl'=>$wallet->getLnurlWithdrawLinkEncoded($access_key=NULL,$params)
        ];
        return $array;
    }

    public function actionLnurlProcessPublic($ott,$access_key=null,$pr=null,$num_satoshis=null,$memo=null,$k1=null,$passThru=null)
    {
        try {
            if ($pr) {
                $result = LnNode::decodeInvoiceHelper($pr);
                $wallet = @Wallet::find()->where(new Expression("JSON_EXTRACT(wallet.json_data, '$.ott.{$ott}') = '{$result['num_satoshis']}'"))->one();
                if (!$wallet) {
                    throw new UnauthorizedHttpException('Invalid satoshi amount');
                }
            } else {
                $wallet = @Wallet::find()->where(new Expression("JSON_EXTRACT(wallet.json_data, '$.ott.{$ott}') = '{$num_satoshis}'"))->one();
                if (!$wallet) {
                    throw new UnauthorizedHttpException('Invalid public LNURL withdraw token');
                }
            }
        } catch (\Throwable $t) {
            return ['status'=>'ERROR','reason'=>$t->getMessage()];
        }


        return $this->actionLnurlProcess($wallet->getFirstAccessKeyByRole(UserAccessKeyBehavior::ROLE_WALLET_ADMIN),$ott,$pr,$num_satoshis,$memo,$k1,$passThru);
    }

    public function actionLnurlProcess($access_key,$ott=null,$pr=null,$num_satoshis=null,$memo=null,$k1=null,$passThru=null)
    {
        $wallet = $this->findByKey($access_key);
        $wallet->updateBalance();
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);

        if ($pr) {

            //Process payment request
            try {

                if ($ott) {
                    $dbOtt = ($wallet->getJsonData('ott')?:[]);
                    if (!array_key_exists($ott,$dbOtt))
                        throw new UnauthorizedHttpException('LNURL is no longer valid: '.\LNPay::$app->request->absoluteUrl);
                } else {
                    $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_PUBLIC_WITHDRAW);
                }

                $model = new LnWalletWithdrawForm();
                $model->payment_request = $pr;
                $model->wallet_id = $wallet->id;

                $pt = @json_decode(@base64_decode($passThru),TRUE);
                $model->passThru = $pt;

                $model->processWithdrawal(['method'=>'lnurl','k1'=>$k1,'ott'=>$ott]);
                \LNPay::$app->response->statusCode = 201;
                $wallet->deleteJsonData([$ott]);
                return ['status'=>'OK'];
            } catch (\Throwable $e) {
                \LNPay::$app->response->statusCode = 200;
                return ['status'=>'ERROR','reason'=>$e->getMessage()];
            }

        } else {

            if (empty($memo)) {
                $memo = \LNPay::$app->name.' Wallet Withdraw';
            }

            $json = [];
            $baseUrl = ["/v1/wallet/{$access_key}/lnurl-process"];
            $json['callback'] = \LNPay::$app->urlManager->createAbsoluteUrl($baseUrl+['ott'=>$ott,'passThru'=>$passThru]);
            $json['k1'] = 'k1';
            $json['maxWithdrawable'] = ($wallet->getIsEligibleToWithdraw($num_satoshis)?$num_satoshis:$wallet->balance)*1000;
            $json['defaultDescription'] = $memo;
            $json['minWithdrawable'] = $json['maxWithdrawable'];
            $json['tag'] = \tkijewski\lnurl\TAG_WITHDRAW;

            if (!$ott) { //only return balanceCheck if this is NOT a disposable LNURL
                $balanceCheck = $wallet->getLnurlWithdrawLinkEncoded($access_key=NULL);
                $json['balanceCheck'] = \tkijewski\lnurl\decodeUrl($balanceCheck)['url'];
            }


            return $json;
        }
    }

    public function actionInvoice($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_DEPOSIT);

        $lnTx = new LnTx();
        $allowedParams = [
            'num_satoshis'=>0,
            'memo' => \LNPay::$app->name.' Invoice',
            'expiry' => 86400,
            'description_hash' => NULL
        ];

        $array = [];
        if ($passThru = \LNPay::$app->request->getBodyParam('passThru')) {
            if (is_array($passThru)) {
                $array = $passThru;
            } else {
                try {
                    $array = Json::decode($passThru);
                } catch (\Throwable $t) {
                    throw new BadRequestHttpException('passThru data must be valid json');
                }
            }
        }
        $lnTx->passThru = $array;
        $lnTx->appendJsonData(['wallet_id'=>$wallet->external_hash]);
        $lnTx->user_id = $wallet->user_id;
        $lnTx->ln_node_id = $wallet->ln_node_id;

        foreach ($allowedParams as $param => $default) {
            $lnTx->{$param} = (\LNPay::$app->request->getBodyParam($param,$default));
        }

        if ($lnTx->validate())
            $lnTxObject = $lnTx->generateInvoice($checkLimits=true);
        else
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($lnTx));

        \LNPay::$app->response->statusCode = 201;
        return LnTx::findOne($lnTxObject->id);
    }

    public function actionTransfer($access_key)
    {
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_TRANSFER);

        $wtf = new WalletTransferForm();
        $wtf->load(\LNPay::$app->request->getBodyParams(),'');
        $wtf->source_wallet_id = $this->findByKey($access_key)->external_hash;

        if ($wtf->validate()) {
            $result = $wtf->executeTransfer();
            $wtx_in = WalletTransaction::findOne($result['wtx_transfer_in']);
            $wtx_out = WalletTransaction::findOne($result['wtx_transfer_out']);
        }
        else
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($wtf));

        \LNPay::$app->response->statusCode = 201;
        return ['wtx_transfer_in'=>$wtx_in->toArray(),'wtx_transfer_out'=>$wtx_out->toArray()];
    }

    public function actionTransactions($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_READ);

        return new \yii\data\ActiveDataProvider([
            'query' => WalletTransaction::find()->where(['wallet_id'=>$wallet->id]),
            'pagination' => [
                'defaultPageSize'=>Yii::$app->request->getQueryParam('page-size')??100,
                'pageSizeLimit'=>[1,500]
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);
    }


}
