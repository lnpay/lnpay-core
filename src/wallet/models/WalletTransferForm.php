<?php
namespace lnpay\wallet\models;


use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\User;
use yii\base\Model;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Signup form
 */
class WalletTransferForm extends Model
{
    public $num_satoshis = NULL;
    public $source_wallet_id = NULL;
    public $dest_wallet_id = NULL;
    public $memo = NULL;
    public $lnPayParams = NULL;
    public $passThru = NULL;

    /**
     * @var bool
     */
    public $safe = false; // if safe, no locking on wallet.

    protected $sourceWalletObject;
    protected $destWalletObject;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num_satoshis','source_wallet_id','dest_wallet_id'], 'required'],
            [['num_satoshis'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            ['safe','boolean'],
            [['memo'],'string'],
            [['lnPayParams'],'checkLnPayParams'],
            [['passThru'],'checkPassThruParams'],
            [['source_wallet_id'],'checkWalletObjects'],
            [['source_wallet_id'],'checkDifferentWallets'],
            ['source_wallet_id','checkSourceBalance']

        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function checkLnPayParams($attribute,$params,$validator)
    {
        if ($this->lnPayParams === NULL)
            return;

        if (is_string($this->lnPayParams)) {
            $json = @json_decode($this->lnPayParams,TRUE);
            if (is_array($json)) {
                $this->lnPayParams = $json;
                return;
            }
        } else if (is_array($this->lnPayParams)) {
            return;
        }
        $this->addError($attribute,"Invalid lnPayParams json specified");
    }

    public function checkPassThruParams($attribute,$params,$validator)
    {
        if ($this->passThru === NULL)
            return;

        if (is_string($this->passThru)) {
            $json = @json_decode($this->passThru,TRUE);
            if (is_array($json)) {
                $this->passThru = $json;
                return;
            }
        } else if (is_array($this->passThru)) {
            return;
        }
        $this->passThru = [];
        $this->addError($attribute,"Invalid passThru json specified");
    }


    public function checkWalletObjects($attribute,$params,$validator)
    {
        if ($this->sourceWalletObject = Wallet::findById($this->source_wallet_id)) {
            //Will probably need this logic eventually
        } else {
            $this->addError($attribute,"Invalid source wallet id");
            return false;
        }

        if ( ($this->destWalletObject = Wallet::findById($this->dest_wallet_id)))  {
            //Will probably need this logic eventually
            if ($this->sourceWalletObject->ln_node_id != $this->destWalletObject->ln_node_id) {
                $this->addError($attribute,"Cannot transfer funds between wallets tied to different nodes");
                return false;
            }

            if ($this->sourceWalletObject->user_id != $this->destWalletObject->user_id) {
                $this->addError($attribute,"Cannot transfer funds");
                \LNPay::error(VarDumper::export($this->attributes),__METHOD__);
                return false;
            }
        } else {
            $this->addError($attribute,"Invalid dest wallet id");
            return false;
        }
    }

    public function checkDifferentWallets($attribute,$params,$validator)
    {
        if ($this->sourceWalletObject->id == $this->destWalletObject->id)
            $this->addError($attribute,"Source and destination wallets cannot be the same!");
    }

    public function checkSourceBalance($attribute,$params,$validator)
    {
        $current_balance = $this->sourceWalletObject->balance;
        if ($this->num_satoshis > $current_balance) {
            $this->addError($attribute,"Insufficient balance in source wallet");
        }
    }

    public function executeTransfer()
    {
        if ($this->safe ||
            \LNPay::$app->mutex->acquire($this->sourceWalletObject->publicId) ||
            $this->destWalletObject->user->getJsonData(User::DATA_IGNORE_WALLET_TRANSFER_MUTEX)) {
            $json_data = [
                'source_wallet_id' => $this->sourceWalletObject->external_hash,
                'dest_wallet_id' => $this->destWalletObject->external_hash,
            ];

            $json_data = ArrayHelper::merge($this->passThru,['lnPayParams' => $this->lnPayParams], $json_data);

            //create debit transaction
            $wtxDebit = new WalletTransaction();
            $wtxDebit->user_id = $this->sourceWalletObject->user_id;
            $wtxDebit->wallet_id = $this->sourceWalletObject->id;
            $wtxDebit->num_satoshis = $this->num_satoshis * -1;
            $wtxDebit->ln_tx_id = null;
            $wtxDebit->user_label = $this->memo;
            $wtxDebit->appendJsonData($json_data);
            if (!$wtxDebit->save()) {
                $this->sourceWalletObject->releaseMutex();
                throw new ServerErrorHttpException(HelperComponent::getFirstErrorFromFailedValidation($wtxDebit));
            }

            //create debit transaction
            $wtxCredit = new WalletTransaction();
            $wtxCredit->user_id = $this->destWalletObject->user_id;
            $wtxCredit->wallet_id = $this->destWalletObject->id;
            $wtxCredit->num_satoshis = (int) $this->num_satoshis;
            $wtxCredit->ln_tx_id = null;
            $wtxCredit->user_label = $this->memo;
            $wtxCredit->appendJsonData($json_data);
            if (!$wtxCredit->save()) {
                throw new ServerErrorHttpException(HelperComponent::getFirstErrorFromFailedValidation($wtxDebit));
            }

            $this->sourceWalletObject->releaseMutex();
            return ['wtx_transfer_out' => $wtxDebit->id, 'wtx_transfer_in' => $wtxCredit->id];
        } else {
            throw new BadRequestHttpException('Wallet busy try again soon...');
        }
    }
}
