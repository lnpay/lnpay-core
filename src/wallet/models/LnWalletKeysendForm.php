<?php
namespace lnpay\wallet\models;

use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\node\components\LndNodeConnector;
use lnpay\exceptions\WalletBusyException;
use lnpay\models\LnTx;
use lnpay\wallet\models\WalletTransaction;
use lnpay\node\models\LnNode;
use yii\base\Model;
use lnpay\models\User;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * Signup form
 */
class LnWalletKeysendForm extends Model
{
    public $dest_pubkey = NULL;
    public $num_satoshis;
    public $custom_records = [];
    public $payment_options = [];
    public $wallet_id;
    public $passThru = [];
    public $fee_limit_msat = NULL;

    public $paidInvoiceObject = null;
    public $lastHop = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num_satoshis','wallet_id','dest_pubkey'], 'required'],
            ['wallet_id','string'],
            ['dest_pubkey', 'string','length'=>66],
            [['num_satoshis','fee_limit_msat'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'type' => 'number'],
            ['dest_pubkey', 'checkWallet'],
            ['dest_pubkey', 'maxWithdraw'],
            ['dest_pubkey', 'callKeysend'],
            ['custom_records','safe'],
        ];
    }

    public function attributeLabels()
    {
        return ['payment_request'=>'Payment Request','wallet_id'=>'Wallet ID'];
    }

    public function getWalletObject()
    {
        return Wallet::find()->where(['external_hash'=>$this->wallet_id])->one();
    }

    public function checkWallet()
    {
        if (!$this->walletObject)
            throw new \yii\web\ServerErrorHttpException('Unknown wallet, cannot continue!');

        $this->walletObject->updateBalance();
    }

    /**
     * check max withdraw
     *
     */
    public function maxWithdraw($attribute,$params,$validator)
    {
        $maxWithdraw = $this->walletObject->balance;
        $invoiceNumSatoshis = $this->num_satoshis;

        if ($invoiceNumSatoshis > $maxWithdraw) {
            $this->addError($attribute,'Invoice too large :) Max balance: '.$maxWithdraw);
            return false;
        }
    }

    public function getRequestParameters()
    {
        return ArrayHelper::merge([
            'dest_pubkey'=>$this->dest_pubkey,
            'amt_msat'=>$this->num_satoshis*1000,
            'custom_records'=>$this->custom_records,
        ],$this->payment_options);
    }

    /**
     * Attempt to pay invoice
     *
     */
    public function callKeysend($attribute,$params,$validator)
    {
        try {
            if ($this->fee_limit_msat !== NULL) {
                $fee_limit_msat = $this->fee_limit_msat;
            } else {
                $fee_limit_msat = 1000 * $this->walletObject->lnNode->getFeeRate(['num_satoshis'=>$this->num_satoshis,'dest_pubkey'=>$this->dest_pubkey]);
            }
            $this->payment_options['fee_limit_msat'] = $fee_limit_msat;

            $rpcConnector = $this->walletObject->lnNode->getLndConnector('RPC');
            $result = $rpcConnector->keysend($this->dest_pubkey,$this->num_satoshis,$this->custom_records,$this->payment_options);
        } catch (\Throwable $t) {
            $this->walletObject->user->registerAction(ActionName::WALLET_SEND_FAILURE,[
                'spontaneous'=>1,
                'wal'=>$this->walletObject->toArray(),
                'node_request_parameters'=>$this->requestParameters,
                'failureReason'=>$t->getMessage(),
                'passThru'=>$this->passThru]
            );

            $this->addError($attribute,$t->getMessage());
            return false;
        }

        \LNPay::info(VarDumper::export($result),__METHOD__);
        $this->paidInvoiceObject = (object) $result;
    }

    public function processKeysend($data=[])
    {
        if (\LNPay::$app->mutex->acquire($this->walletObject->publicId) || ($this->walletObject->balance > ($this->num_satoshis*10) ) ) {

            if ($this->validate(['num_satoshis','fee_limit_msat']) && $this->validate()) {
                //Carry on!
            } elseif (!$this->hasErrors()) {
                throw new ServerErrorHttpException('Failed to keysend for unknown reason');
            } else {
                $this->walletObject->releaseMutex();
                throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($this));
            }

            if ($this->paidInvoiceObject) {

                $arrayPaidInvoiceObject = (array) $this->paidInvoiceObject;
                $outgoingChanId = @$arrayPaidInvoiceObject['htlcs'][0]['route']['hops'][0]['chanId'];

                foreach ($this->paidInvoiceObject->htlcs as $htlc) {
                    foreach ($htlc['route']['hops'] as $hop) {
                        if ($hop['pubKey'] == $this->dest_pubkey) {
                            $this->lastHop = $hop;
                        }
                    }
                }

                $customRecords = $this->lastHop['customRecords'];
                array_walk($customRecords,function(&$item1, $key){
                    $item1 = base64_decode($item1);
                    if ($key == LndNodeConnector::KEYSEND_TLV_KEY)
                        $item1 = bin2hex($item1);
                });

                $lnTx = new LnTx();
                $lnTx->user_id = $this->walletObject->user_id;
                $lnTx->num_satoshis = $this->paidInvoiceObject->valueSat;
                $lnTx->fee_msat = @$this->paidInvoiceObject->feeMsat;
                $lnTx->memo = $this->num_satoshis.' keysend to '.$this->dest_pubkey;
                $lnTx->payment_request = $this->dest_pubkey.':'.$this->num_satoshis;
                $lnTx->r_hash_decoded = $this->paidInvoiceObject->paymentHash;
                $lnTx->dest_pubkey = $this->dest_pubkey;
                $lnTx->expires_at = NULL;
                $lnTx->payment_preimage = $this->paidInvoiceObject->paymentPreimage;
                $lnTx->settled = 1;
                $lnTx->settled_at = time();
                $lnTx->is_keysend = true;
                $lnTx->custom_records = $customRecords;
                $lnTx->passThru = $this->passThru;
                $lnTx->ln_node_id = $this->walletObject->ln_node_id;
                $lnTx->appendJsonData(ArrayHelper::merge($data,['outgoingChanId'=>$outgoingChanId]));

                if ($lnTx->save()) {
                    //good to go
                } else {
                    throw new \Exception (HelperComponent::getFirstErrorFromFailedValidation($lnTx));
                }

                $wtx = new WalletTransaction();
                $wtx->user_id = $this->walletObject->user_id;
                $wtx->wallet_id = $this->walletObject->id;
                $wtx->num_satoshis = $this->num_satoshis*-1;
                $wtx->ln_tx_id = $lnTx->id;
                $wtx->user_label = $lnTx->memo;
                $wtx->passThru = $this->passThru;
                $wtx->appendJsonData($data);

                if ($wtx->save()) {
                    $this->walletObject->releaseMutex();
                    return $wtx;
                } else {
                    throw new \Exception ('Unable to attach paid withdraw invoice to wallet:'.HelperComponent::getFirstErrorFromFailedValidation($lnTx));
                }

            } else {
                throw new \yii\web\ServerErrorHttpException('Issue processing keysend');
            }
        } else {
            throw new WalletBusyException($this->walletObject->publicId);
        }
    }
}
