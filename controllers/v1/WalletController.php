<?php

namespace app\controllers\v1;

use app\behaviors\UserAccessKeyBehavior;
use app\components\HelperComponent;
use app\jobs\LnWalletKeysendFormJob;
use app\models\LnTx;
use app\models\User;
use app\models\wallet\LnWalletKeysendForm;
use app\models\wallet\WalletTransaction;
use app\models\wallet\LnWalletWithdrawForm;
use app\models\wallet\Wallet;
use app\models\wallet\WalletTransactionSearch;
use app\models\wallet\WalletTransferForm;

use app\modules\node\models\LnNode;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class WalletController extends BaseApiController
{
    public $modelClass = 'app\models\wallet\Wallet';

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

    public function findByKey($access_key)
    {
        $modelClass = $this->modelClass;

        $apiKey = @Yii::$app->user->identity->sessionApiKey;

        //If SAK is used, all wallet keys are valid
        if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_SECRET_API_KEY,$apiKey)) {
            $wallet = $modelClass::findById($access_key) ?? $modelClass::findByKey($access_key) ?? NULL;
            if ($wallet) {
                if ($wallet->user_id == Yii::$app->user->id) { //make sure this is the right user
                    return $wallet;
                }
            }

        }
        //public access key
        else if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY,Yii::$app->user->identity->sessionApiKey)) {
            $wallet = $modelClass::findByKey($access_key);
            if ($wallet) {
                if (Yii::$app->user->id == $wallet->user_id) {
                    return $wallet;
                }
            }
        } else { //publicly available with no key needed
            if (in_array($this->action->id,['lnurl-process','lnurl-process-public'])) { //if lnurl which grants public access based on key
                $wallet = $modelClass::findByKey($access_key);
                if ($wallet) {
                    return $wallet;
                }
            }
        }

        if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY,Yii::$app->user->identity->sessionApiKey)) {
            throw new UnauthorizedHttpException('Invalid Wallet Access Key. Keys prefixed with waka_, waki_, wakr, waklw are valid when using pak_');
        }
        throw new UnauthorizedHttpException('Wallet not found: '.$access_key);
    }

    public function checkAccessKey($item,$access_key=NULL)
    {
        if (!$access_key) //assuming it's a wallet access key if it's in the URL
            $access_key = Yii::$app->request->getQueryParam('access_key');

        if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_SECRET_API_KEY,Yii::$app->user->identity->sessionApiKey)) {
            return true;
        } else if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_KEY_SUSPENDED,$access_key)) {
            throw new UnauthorizedHttpException('Key has been suspended');
        } else if (UserAccessKeyBehavior::checkKeyAccess($item,$access_key))
            return true;
        else
            throw new UnauthorizedHttpException(UserAccessKeyBehavior::getAccessKeyPrefix($access_key).' access key provided does not permission to do this: '.$item);

    }

    public function actionCreate()
    {
        $model = new Wallet();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->user_id = Yii::$app->user->id;

        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);

            $wallet = Wallet::findOne($model->id);
            return ArrayHelper::merge($wallet->toArray(),['access_keys'=>$wallet->userAccessKeys]);

        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException(HelperComponent::getErrorStringFromInvalidModel($model));
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
            'query' => $modelClass::find()->where(['user_id'=>Yii::$app->user->id]),
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
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->wallet_id = $wallet->id;
        Yii::$app->getRequest()->getBodyParam('passThru',[]);

        $array = [];
        if ($passThru = Yii::$app->request->getBodyParam('passThru')) {
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
        Yii::$app->response->statusCode = 201;
        return WalletTransaction::findOne($wtx->id);
    }

    public function actionKeysend($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);

        if ($this->isAsync) {
            $id = Yii::$app->queue->push(new LnWalletKeysendFormJob([
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
            $wtx_id = $job->execute(Yii::$app->queue);
        }

        Yii::$app->response->statusCode = 201;
        return WalletTransaction::findOne($wtx_id);
    }

    public function actionLnurlWithdraw($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);

        $params = [
            'num_satoshis'=>Yii::$app->request->getQueryParam('num_satoshis'),
            'memo'=>Yii::$app->request->getQueryParam('memo'),
            'ott'=>HelperComponent::generateRandomString(12),
            'passThru'=>Yii::$app->request->getQueryParam('passThru'),
            'public'=>Yii::$app->request->getQueryParam('public')
            ];
        $array = [
            'lnurl'=>$wallet->getLnurl($access_key,$params),
            'ott'=>$params['ott']
        ];
        return $array;
    }

    public function actionLnurlWithdrawStatic($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::ROLE_WALLET_ADMIN);

        $params = [
            'num_satoshis'=>Yii::$app->request->getQueryParam('num_satoshis'),
            'memo'=>Yii::$app->request->getQueryParam('memo'),
            'passThru'=>Yii::$app->request->getQueryParam('passThru')
        ];
        $array = [
            'lnurl'=>$wallet->getLnurl($access_key=NULL,$params)
        ];
        return $array;
    }

    public function actionLnurlProcessPublic($ott,$access_key=null,$pr=null,$num_satoshis=null,$memo=null,$k1=null,$passThru=null)
    {
        try {
            if ($pr) {
                $result = LnNode::getLnpayNodeQuery()->one()->getLndConnector()->decodeInvoice($pr);
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
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);

        if ($pr) {

            //Process payment request
            try {

                if ($ott) {
                    $dbOtt = ($wallet->getJsonData('ott')?:[]);
                    if (!array_key_exists($ott,$dbOtt))
                        throw new UnauthorizedHttpException('LNURL is no longer valid: '.Yii::$app->request->absoluteUrl);
                } else {
                    $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_PUBLIC_WITHDRAW);
                }

                $model = new LnWalletWithdrawForm();
                $model->payment_request = $pr;
                $model->wallet_id = $wallet->id;

                $pt = @json_decode(@base64_decode($passThru),TRUE);
                $model->passThru = $pt;

                $model->processWithdrawal(['method'=>'lnurl','k1'=>$k1,'ott'=>$ott]);
                Yii::$app->response->statusCode = 201;
                $wallet->deleteJsonData([$ott]);
                return ['status'=>'OK'];
            } catch (\Throwable $e) {
                Yii::$app->response->statusCode = 200;
                return ['status'=>'ERROR','reason'=>$e->getMessage()];
            }

        } else {

            if (empty($memo)) {
                $memo = Yii::$app->name.' Wallet Withdraw';
            }

            $json = [];
            if (Yii::$app->controller->action->id == 'lnurl-process-public')
                $baseUrl = ["/v1/wallet/lnurl-process-public"];
            else
                $baseUrl = ["/v1/wallet/{$access_key}/lnurl-process"];
            $json['callback'] = Yii::$app->urlManager->createAbsoluteUrl($baseUrl+['ott'=>$ott,'passThru'=>$passThru]);
            $json['k1'] = 'k1';
            $json['maxWithdrawable'] = ($wallet->getIsEligibleToWithdraw($num_satoshis)?$num_satoshis:$wallet->balance)*1000;
            $json['defaultDescription'] = $memo;
            $json['minWithdrawable'] = 0;
            $json['tag'] = \tkijewski\lnurl\TAG_WITHDRAW;

            if (!$ott) { //only return balanceCheck if this is NOT a disposable LNURL
                $balanceCheck = $wallet->getLnurl($access_key=NULL);
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
            'memo' => Yii::$app->name.' Invoice',
            'expiry' => 86400,
            'description_hash' => NULL
        ];

        $array = [];
        if ($passThru = Yii::$app->request->getBodyParam('passThru')) {
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
            $lnTx->{$param} = (Yii::$app->request->getBodyParam($param,$default));
        }

        if ($lnTx->validate())
            $lnTxObject = $lnTx->generateInvoice($checkLimits=true);
        else
            throw new BadRequestHttpException(HelperComponent::getErrorStringFromInvalidModel($lnTx));

        Yii::$app->response->statusCode = 201;
        return LnTx::findOne($lnTxObject->id);
    }

    public function actionTransfer($access_key)
    {
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_TRANSFER);

        $wtf = new WalletTransferForm();
        $wtf->load(Yii::$app->request->getBodyParams(),'');
        $wtf->source_wallet_id = $this->findByKey($access_key);

        if ($wtf->validate()) {
            $result = $wtf->executeTransfer();
            $wtx_in = WalletTransaction::findOne($result['wtx_transfer_in']);
            $wtx_out = WalletTransaction::findOne($result['wtx_transfer_out']);
        }
        else
            throw new BadRequestHttpException(HelperComponent::getErrorStringFromInvalidModel($wtf));

        Yii::$app->response->statusCode = 201;
        return ['wtx_transfer_in'=>$wtx_in->toArray(),'wtx_transfer_out'=>$wtx_out->toArray()];
    }

    public function actionTransactions($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::ROLE_WALLET_ADMIN);

        return new \yii\data\ActiveDataProvider([
            'query' => WalletTransaction::find()->where(['wallet_id'=>$wallet->id]),
            'pagination' => [
                'defaultPageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);
    }


}
