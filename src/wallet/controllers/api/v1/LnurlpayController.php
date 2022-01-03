<?php

namespace lnpay\wallet\controllers\api\v1;

use lnpay\wallet\models\WalletLnurlpay;
use lnpay\base\ApiController;
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
            'view-all'=>['GET','OPTIONS']
        ];
    }

    public function actionLnurlProcess($access_key,$wallet_lnurlpay_id,$amount=null)
    {
        try {
            $w = $this->findByKey($access_key);
            $lnurlpModel = WalletLnurlpay::findByHash($wallet_lnurlpay_id);

            if ( (!$w || !$lnurlpModel) || ($w->id != $lnurlpModel->wallet_id)) {
                throw new UnauthorizedHttpException("Wallet or lnurlpay link is not valid");
            }

            if ($amount) { //issue the callback with PR
                //checks
                if ($amount < $lnurlpModel->lnurlp_minSendable_msat ||
                    $amount > $lnurlpModel->lnurlp_maxSendable_msat
                ) {
                    $satMin = $lnurlpModel->lnurlp_minSendable_msat/1000;
                    $satMax = $lnurlpModel->lnurlp_maxSendable_msat/1000;
                    return ["status"=>"ERROR","reason"=>"{$amount} sat is not within {$satMin} - {$satMax} sat"];
                }


                $lnTx = $w->generateLnInvoice(
                    [
                        'description_hash' => hash('sha256',utf8_encode($lnurlpModel->lnurlp_metadata)),
                        'memo'=>'LNURL PAY (via LNPay.co)'
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
                    'metadata'          => $lnurlpModel->lnurlp_metadata
                ];
            }
        } catch ( \Throwable $t) {
            return ["status"=>"ERROR","reason"=>$t->getMessage()];
        }

    }



}
