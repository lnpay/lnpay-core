<?php
namespace lnpay\wallet\models;

use lnpay\components\HelperComponent;
use lnpay\models\LnTx;
use lnpay\wallet\models\WalletTransaction;
use yii\base\Model;
use lnpay\models\User;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * Signup form
 */
class LnLoopOutForm extends Model
{
    public $num_satoshis;
    public $addr;
    public $channel;
    public $wallet_id;
    public $fast=true;
    public $conf_target=2;
    public $htlc_confs=1;
    public $label;

    public function beforeValidate()
    {
        $this->walletObject->updateBalance();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num_satoshis','addr'], 'required'],
            [['num_satoshis','channel'], 'integer'],
            [['label','wallet_id','addr'],'string'],
            [['fast'],'boolean'],
            [['wallet_id'],'checkWallet'],
            [['num_satoshis'],'checkMaxWithdraw'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'addr'=>'Destination Address',
            'num_satoshis'=>'Amount (sats)',
            'channel'=>'Outbound Channel'
        ];
    }

    public function getWalletObject()
    {
        return Wallet::findById($this->wallet_id);
    }

    public function checkWallet()
    {
        if (!$this->walletObject)
            throw new \yii\web\ServerErrorHttpException('Unknown wallet, cannot continue!');
    }

    /**
     * check max withdraw
     *
     */
    public function checkMaxWithdraw($attribute,$params,$validator)
    {
        $maxWithdraw = $this->walletObject->availableBalance;

        if ($this->num_satoshis > $maxWithdraw) {
            $this->addError($attribute,'Not enough sats in wallet! Max balance: '.$maxWithdraw);
            return false;
        }
    }

    public function attemptLoopOut()
    {
        //Actually call the loop function

        //Register the transactions
        $wtxDebit = new WalletTransaction();
        $wtxDebit->wtx_type_id = WalletTransactionType::LN_LOOP_OUT;
        $wtxDebit->user_id = $this->walletObject->user_id;
        $wtxDebit->wallet_id = $this->walletObject->id;
        $wtxDebit->num_satoshis = $this->num_satoshis * -1;
        $wtxDebit->ln_tx_id = null;
        $wtxDebit->user_label = $this->label. ' (loop out)';
        $wtxDebit->appendJsonData(['dest_address'=>$this->addr]);
        if (!$wtxDebit->save()) {
            throw new ServerErrorHttpException(HelperComponent::getFirstErrorFromFailedValidation($wtxDebit));
        }

        //Success!
    }
}
