<?php

namespace lnpay\wallet\controllers\api\v1;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\CustyDomain;
use lnpay\wallet\exceptions\InvalidLnurlpayLinkException;
use lnpay\wallet\exceptions\UnableToCreateLnurlpayException;
use lnpay\wallet\exceptions\UnableToPayLnurlpayException;
use lnpay\wallet\exceptions\UnableToUpdateLnurlpayException;
use lnpay\wallet\models\LnWalletLnurlpayPayForm;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\wallet\models\WalletLnurlpay;
use lnpay\base\ApiController;
use lnpay\wallet\models\WalletTransactionType;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class LnurlpayController extends ApiController
{
    /**
     * @var array restrict the following endpoints to sak only
     */
    public $sakOnlyArray = [
        'api/v1/lnurlpay/update'
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
            'lightning-address'=>   ['GET','OPTIONS'],
            'probe'=>['GET','OPTIONS']
        ];
    }

    public static function findModel($wallet_lnurlpay_id)
    {
        return WalletLnurlpay::find()->where(['external_hash'=>$wallet_lnurlpay_id,'user_id'=>\LNPay::$app->user->id])->one();
    }

    public function actionView($wallet_lnurlpay_id)
    {
        if ($l = static::findModel($wallet_lnurlpay_id)) {
            return $l;
        } else {
            throw new InvalidLnurlpayLinkException('Unknown lnurlpay id');
        }
    }

    public function actionUpdate($wallet_lnurlpay_id)
    {
        if ($l = static::findModel($wallet_lnurlpay_id)) {
            $l->load(\LNPay::$app->request->post(),'');
            if ($l->validate() && $l->save()) {
                return static::findModel($wallet_lnurlpay_id);
            } else {
                throw new UnableToUpdateLnurlpayException(HelperComponent::getFirstErrorFromFailedValidation($l));
            }
        } else {
            throw new InvalidLnurlpayLinkException('Unknown lnurlpay id');
        }
    }

    public function actionCreate($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::ROLE_WALLET_ADMIN);

        $l = new WalletLnurlpay();
        $l->load(\LNPay::$app->request->post(),'');

        $l->user_id = \LNPay::$app->user->id;
        $l->wallet_id = $wallet->id;
        if ($cdi = \LNPay::$app->request->post('custy_domain_id')) {
            if ($cd = CustyDomain::findByHash($cdi))
                $l->custy_domain_id = $cd->id;
            else
                throw new UnableToCreateLnurlpayException('Invalid custy_domain_id');
        }

        if ($l->validate() && $l->save()) {
            return $l;
        } else {
            throw new UnableToCreateLnurlpayException(HelperComponent::getFirstErrorFromFailedValidation($l));
        }
    }

    /*
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
    public function actionLightningAddress($username)
    {
        $prefix = explode("_",$username);
        if ($prefix[0] == 'lnurlp') {
            $lnurlpModel = WalletLnurlpay::findByHash($username);
        }
        $referrer = parse_url(\LNPay::$app->request->referrer);
        if (@$referrer['host']) {
            $host = $referrer['host'];
        } else {
            $host = parse_url(\LNPay::$app->request->absoluteUrl)['host'];
        }
        if ($cd = CustyDomain::find()->where(['domain_name'=>$host])->one()) {
            //This is a ln address with a domain that is in our system!
            $lnurlpModel = WalletLnurlpay::find()
                ->where(['lnurlp_identifier'=>$username,'custy_domain_id'=>$cd->id])
                ->one();
        }

        if (@$lnurlpModel) {
            $access_key = $lnurlpModel->wallet->getFirstAccessKeyByRole(UserAccessKeyBehavior::ROLE_WALLET_LNURL_PAY);
            return $this->actionLnurlProcess($access_key,$username);
        } else {
            throw new BadRequestHttpException('Invalid username ('.$username.') for domain:'.$host);
        }


    }

    public function actionLnurlProcess($access_key,$wallet_lnurlpay_id,$amount=null,$comment=null)
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
                        'memo'=>($comment??'LNURL PAY')
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
                    'commentAllowed'    => $lnurlpModel->lnurlp_commentAllowed??0,
                    'tag'               => 'payRequest',
                    'metadata'          => $lnurlpModel->lnurlp_metadata,
                    'callback'          => $lnurlpModel->lnurl_decoded
                ];
            }
        } catch ( \Throwable $t) {
            return ["status"=>"ERROR","reason"=>$t->getMessage()];
        }
    }

    public function actionProbe($lnurlpayEncodedOrLnAddress)
    {
        try {
            if (stripos($lnurlpayEncodedOrLnAddress,'@')!==FALSE) {
                $url = static::getUrlFromLnAddress($lnurlpayEncodedOrLnAddress);
            } else if ($lnurlp = \tkijewski\lnurl\decodeUrl($lnurlpayEncodedOrLnAddress)) {
                if (@$lnurlp['url']) {
                    $url = $lnurlp['url'];
                } else {
                    throw new InvalidLnurlpayLinkException('invalid lnurlpay link');
                }
            } else {
                throw new \Exception('lnurlpay_encoded or ln_address must be specified');
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

    public function actionPay($access_key)
    {
        $wallet = $this->findByKey($access_key);
        $this->checkAccessKey(UserAccessKeyBehavior::PERM_WALLET_WITHDRAW);

        $form = new LnWalletLnurlpayPayForm();
        $form->load(\LNPay::$app->getRequest()->getBodyParams(),'');
        $form->probe_json = $this->actionProbe($form->lnurlpay_encoded??$form->ln_address);


        $array = [];
        $bp = \LNPay::$app->getRequest()->getBodyParams();
        if ($passThru = @$bp['passThru']) {
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
        if ($form->ln_address) {
            $array['target_ln_address'] = $form->ln_address;
        }
        if ($form->lnurlpay_encoded) {
            $array['target_lnurlp_encoded'] = $form->lnurlpay_encoded;
        }
        $form->passThru = $array;

        if ($form->validate()) {
            $invoice = $form->requestRemoteInvoice();

            $model = new LnWalletWithdrawForm();
            $model->payment_request = $invoice;
            $model->wallet_id = $wallet->id;
            $model->passThru = $form->passThru;
            $model->target_msat = $form->amt_msat;
            $model->wtx_type_id = WalletTransactionType::LN_LNURL_PAY_OUTBOUND;

            return $model->processWithdrawal(['method'=>'lnurlpay','lnurlp_comment'=>$form->comment]);

        } else {
            throw new UnableToPayLnurlpayException(HelperComponent::getFirstErrorFromFailedValidation($form));
        }


    }

    public static function getUrlFromLnAddress($lnAddress)
    {
        $username = explode('@',$lnAddress)[0];
        $domain = explode('@',$lnAddress)[1];
        if (YII_ENV_TEST)
            $url = 'http://localhost/index-test.php/.well-known/lnurlp/'.$username;
        else
            $url = 'https://'.$domain.'/.well-known/lnurlp/'.$username;

        return $url;
    }



}
