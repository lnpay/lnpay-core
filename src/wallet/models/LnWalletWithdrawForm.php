<?php
namespace lnpay\wallet\models;

use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\models\BalanceWithdraw;
use lnpay\models\LnTx;
use lnpay\node\exceptions\UnableToPayInvoiceException;
use lnpay\wallet\models\WalletTransaction;
use yii\base\Model;
use lnpay\models\User;

use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * Signup form
 */
class LnWalletWithdrawForm extends Model
{
    public $num_satoshis = NULL;
    public $fee_limit_msat = NULL;
    public $payment_request;
    public $paidInvoiceObject = null;
    public $decodedInvoiceObject = null;
    public $wallet_id = null;
    public $passThru = [];
    public $wtx_type_id = null;
    public $target_msat = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_request','wallet_id'], 'required'],
            [['wallet_id','fee_limit_msat','wtx_type_id'],'integer'],
            [['fee_limit_msat'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'type' => 'number'],
            ['payment_request', 'string'],
            ['payment_request', 'checkWallet'],
            ['payment_request', 'checkInvoice'],
            ['payment_request', 'checkAccountLimit'],
            ['payment_request', 'maxWithdraw'],
            ['payment_request', 'payInvoice']
        ];
    }

    public function attributeLabels()
    {
        return ['payment_request'=>'Payment Request','wallet_id'=>'Wallet ID'];
    }

    public function getWalletObject()
    {
        return Wallet::findOne($this->wallet_id);
    }

    public function checkWallet()
    {
        if (!$this->walletObject)
            throw new \yii\web\ServerErrorHttpException('Unknown wallet, cannot continue!');

        $this->walletObject->updateBalance();
    }

    public function checkAccountLimit($attribute,$params)
    {
        if ($max = $this->walletObject->user->getJsonData(User::DATA_MAX_WITHDRAWAL)) {
            if ($this->decodedInvoiceObject->num_satoshis > $max) {
                $this->addError($attribute,'Sends are limited to: '.$max.' satoshi per request');
                return false;
            }
        } else if ($this->num_satoshis > User::USER_GLOBAL_MAX_LIMIT_SATS) {
            $this->addError($attribute,'Sends are limited to: '.User::USER_GLOBAL_MAX_LIMIT_SATS.' satoshi per request');
            return false;
        }
    }
    /**
     * Check if valid LN Invoice
     *
     */
    public function checkInvoice($attribute,$params,$validator)
    {
        try{
            $result = $this->walletObject->lnNode->getLndConnector('RPC')->decodeInvoice($this->payment_request);
        } catch (\Throwable $t) {
            $this->addError($attribute,$t->getMessage());
            return false;
        }

        //transition from REST to RPC keep it as object
        $result = (object) $result;

        if (!@$result->num_satoshis) {
            $this->addError($attribute,'Cannot process any amount invoices');
        } else {
            $this->decodedInvoiceObject = $result;
        }
    }

    /**
     * check max withdraw
     *
     */
    public function maxWithdraw($attribute,$params,$validator)
    {
        $maxWithdraw = $this->walletObject->balance;
        $invoiceNumSatoshis = $this->decodedInvoiceObject->num_satoshis;
        $invoiceNumMsat = $this->decodedInvoiceObject->numMsat;

        if ($invoiceNumSatoshis > $maxWithdraw) {
            $this->addError($attribute,'Invoice too large :) Max balance: '.$maxWithdraw);
            return false;
        }

        if ($this->target_msat) {
            if ($this->target_msat != $invoiceNumMsat) {
                $this->addError($attribute,"Requested invoice ({$this->target_msat}) and invoice generated ({$invoiceNumMsat}) do not match.");
                return false;
            }
        }

    }

    public function getRequestParameters()
    {
        return ArrayHelper::merge(['payment_request'=>$this->payment_request,'fee_limit_msat'=>$this->fee_limit_msat],[]);
    }

    /**
     * Attempt to pay invoice
     *
     */
    public function payInvoice($attribute,$params,$validator)
    {
        try {
            if ($this->fee_limit_msat !== NULL) {
                $this->fee_limit_msat = $this->fee_limit_msat;
            } else {
                $this->fee_limit_msat = $this->walletObject->lnNode->getFeeRate($this->decodedInvoiceObject) * 1000;
            }

            $result = $this->walletObject->payLnInvoice($this->payment_request,['fee_limit_msat'=>$this->fee_limit_msat]);
            if (!empty($result->payment_error)) {
                throw new UnableToPayInvoiceException($result->payment_error);
            }
        } catch (\Throwable $t) {
            $this->walletObject->user->registerAction(ActionName::WALLET_SEND_FAILURE,[
                    'spontaneous'=>0,
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

    public function processWithdrawal($data=[])
    {
        if (\LNPay::$app->mutex->acquire($this->walletObject->publicId)) {

            if ($this->validate(['fee_limit_msat']) && $this->validate()) {
                //Carry on!
            } elseif (!$this->hasErrors()) {
                throw new ServerErrorHttpException('Failed to withdraw for unknown reason');
            } else {
                $this->walletObject->releaseMutex();
                throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($this));
            }

            $arrayPaidInvoiceObject = (array) $this->paidInvoiceObject;
            if ($this->decodedInvoiceObject && $this->paidInvoiceObject) {
                $lnTx = new LnTx();
                $lnTx->user_id = $this->walletObject->user_id;
                $lnTx->num_satoshis = $this->decodedInvoiceObject->num_satoshis;
                $lnTx->fee_msat = @$this->paidInvoiceObject->feeMsat;
                $lnTx->memo = @$this->decodedInvoiceObject->description;
                $lnTx->payment_request = $this->payment_request;
                $lnTx->r_hash_decoded = $this->paidInvoiceObject->payment_hash;
                $lnTx->dest_pubkey = $this->decodedInvoiceObject->destination;
                $lnTx->expires_at = time() + $this->decodedInvoiceObject->expiry;
                $lnTx->payment_preimage = $this->paidInvoiceObject->payment_preimage;
                $lnTx->settled = 1;
                $lnTx->settled_at = time();
                $lnTx->passThru = $this->passThru;
                $lnTx->ln_node_id = $this->walletObject->ln_node_id;
                $lnTx->appendJsonData(ArrayHelper::merge($data,['outgoingChannelId'=>@$arrayPaidInvoiceObject['htlcs'][0]['route']['hops'][0]['chanId']]));

                if ($lnTx->save()) {
                    //good to go
                } else {
                    throw new \Exception (HelperComponent::getFirstErrorFromFailedValidation($lnTx));
                }

                $wtx = new WalletTransaction();
                $wtx->user_id = $this->walletObject->user_id;
                $wtx->wallet_id = $this->walletObject->id;
                $wtx->num_satoshis = $this->decodedInvoiceObject->num_satoshis*-1;
                $wtx->ln_tx_id = $lnTx->id;
                $wtx->user_label = $lnTx->memo;
                $wtx->wtx_type_id = ($this->wtx_type_id?:WalletTransactionType::LN_WITHDRAWAL);
                $wtx->passThru = $this->passThru;
                $wtx->appendJsonData($data);

                if ($wtx->save()) {
                    $this->walletObject->releaseMutex();
                    return $wtx;
                } else {
                    throw new \Exception ('Unable to attach paid withdraw invoice to wallet:'.$lnTx->id);
                }

            } else {
                throw new \yii\web\ServerErrorHttpException('Issue processing withdrawal');
            }
        } else {
            throw new BadRequestHttpException('Wallet busy try again soon');
        }
    }
}
