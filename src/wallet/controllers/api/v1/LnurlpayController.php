<?php

namespace lnpay\wallet\controllers\api\v1;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\wallet\exceptions\InvalidLnurlpayLinkException;
use lnpay\wallet\models\WalletLnurlpay;
use lnpay\base\ApiController;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class LnurlpayController extends ApiController
{
    /**
     * @var array restrict the following endpoints to sak only
     */
    public $sakOnlyArray = [
        //'api/v1/lnurlpay/view-all',
    ];

    public $modelClass = 'lnpay\wallet\models\WalletLnurlpay';

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
            'view-all'=>['GET','OPTIONS'],
            'probe'=>['GET','OPTIONS']
        ];
    }

/*
    public function actionView()
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
*/

    public function actionLnurlProcess($access_key,$wallet_lnurlpay_id,$amount=null)
    {
        try {
            $w = $this->findByKey($access_key);
            $lnurlpModel = WalletLnurlpay::findByHash($wallet_lnurlpay_id);

            if ( (!$w || !$lnurlpModel) || ($w->id != $lnurlpModel->wallet_id) || !$lnurlpModel->isActive) {
                throw new UnauthorizedHttpException("Wallet or lnurlpay link is not valid or active");
            }

            if ($amount) { //issue the callback with PR
                //checks
                if ($amount < $lnurlpModel->lnurlp_minSendable_msat ||
                    $amount > $lnurlpModel->lnurlp_maxSendable_msat
                ) {
                    $satMin = $lnurlpModel->lnurlp_minSendable_msat/1000;
                    $satMax = $lnurlpModel->lnurlp_maxSendable_msat/1000;
                    $satAmount = $amount/1000;
                    return ["status"=>"ERROR","reason"=>"{$satAmount} sat is not within {$satMin} - {$satMax} sat"];
                }


                $lnTx = $w->generateLnInvoice(
                    [
                        'num_satoshis'=>ceil($amount/1000),
                        'description_hash' => hash('sha256',utf8_encode($lnurlpModel->lnurlp_metadata)),
                        'memo'=>'LNURL PAY'
                    ],
                    \LNPay::$app->request->getQueryParams()
                );
                $array = [
                    'pr'        =>  $lnTx->payment_request,
                    'routes'    =>  [],
                ];

                //add successAction and other things


                return $array;
            } else {
                return [
                    'minSendable'       => $lnurlpModel->lnurlp_minSendable_msat,
                    'maxSendable'       => $lnurlpModel->lnurlp_maxSendable_msat,
                    'commentAllowed'    => 0,
                    'tag'               => 'payRequest',
                    'metadata'          => $lnurlpModel->lnurlp_metadata,
                    'callback'          => $lnurlpModel->lnurl_decoded
                ];
            }
        } catch ( \Throwable $t) {
            return ["status"=>"ERROR","reason"=>$t->getMessage()];
        }
    }

    public function actionProbe($lnurlpay_encoded)
    {
        try {
            $lnurlp = \tkijewski\lnurl\decodeUrl($lnurlpay_encoded);
            if (@$lnurlp['url']) {
                $url = $lnurlp['url'];
            } else {
                throw new InvalidLnurlpayLinkException('invalid lnurlpay link');
            }

            $client = new \GuzzleHttp\Client([
                'curl'=> [],
                'http_errors'=>true,
                'headers' => ['SERVICE'=>'LNPAY-PROBE'],
                'debug'=>false
            ]);

            $r = null;
            $response = $client->request('GET', $url);
            $r = $response->getBody()->getContents();

        } catch (\Throwable $t) {
            throw new InvalidLnurlpayLinkException($t->getMessage());
        }

        $json = json_decode($r,TRUE);
        if (@$json['metadata'])
            $json['metadata'] = json_decode($json['metadata'],TRUE);

        return $json;

    }




}
