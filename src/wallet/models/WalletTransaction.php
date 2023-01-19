<?php

namespace lnpay\wallet\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\models\LnTx;
use lnpay\models\User;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wallet_transaction".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 * @property int $wallet_id
 * @property int $num_satoshis
 * @property int|null $ln_tx_id
 * @property int|null $wtx_type_id
 * @property string|null $user_label
 * @property string $external_hash
 * @property string|null $json_data
 *
 * @property User $user
 * @property Wallet $wallet
 * @property LnTx $lnTx
 */
class WalletTransaction extends \yii\db\ActiveRecord
{
    public $_passThru = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_transaction';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
            JsonDataBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet_id', 'num_satoshis'], 'required'],
            ['num_satoshis', 'compare', 'compareValue' => 0, 'operator' => '!=', 'type' => 'number'],
            [['external_hash'],'default','value'=>function($model,$attribute) { return 'wtx_'.HelperComponent::generateRandomString(24); }],
            [['user_id', 'wallet_id', 'num_satoshis', 'ln_tx_id','wtx_type_id','wallet_lnurlpay_id','wallet_lnurlw_id'], 'integer'],
            [['json_data'], 'safe'],
            [['user_label'], 'string', 'max' => 255],
            ['passThru',function ($attribute, $params) {
                if(!is_array($this->$attribute)){
                    $this->addError($attribute,'Pass thru must be a valid JSON');
                }
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'wallet_id' => 'Wallet ID',
            'num_satoshis' => 'Num Satoshis',
            'ln_tx_id' => 'Ln Tx ID',
            'user_label' => 'User Label',
            'external_hash' => 'External Hash',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWallet()
    {
        return $this->hasOne(Wallet::className(), ['id' => 'wallet_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLnTx()
    {
        return $this->hasOne(LnTx::className(), ['id' => 'ln_tx_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWalletTransactionType()
    {
        return $this->hasOne(WalletTransactionType::className(), ['id' => 'wtx_type_id']);
    }

    public function setPassThru($data)
    {
        if (!is_array($data))
            $this->_passThru = [];
        else
            $this->_passThru = $data;
    }

    public function getPassThru()
    {
        return $this->_passThru;
    }

    /**
     * @return WalletTransaction|null
     * @throws \Exception
     */
    public function createNetworkFeeTransaction()
    {
        //Network fee
        if ($this->lnTx && $this->lnTx->fee_msat > 0) {
            $networkFee = (int) ceil($this->lnTx->fee_msat/1000)*-1;
            \LNPay::info('tx: '.$this->id.' - Network fee:'.$networkFee,__METHOD__);
            $wtx = new WalletTransaction();
            $wtx->user_id = $this->user_id;

            if ($this->user->feeTargetWallet==User::DATA_FEE_TARGET_WALLET_CONTAINED) {
                if ( ($this->wallet->balance + $networkFee) < 0) { //this will result in negative balance to wallet
                    if ($this->wallet->lnNode->is_custodian) {
                        $wtx->wallet_id = $this->wallet_id; //this deducts from existing wallet and will make it go negative (not ideal)
                    } else {
                        $wtx->wallet_id = $this->user->fee_wallet_id; //send to fee wallet
                    }

                } else { //send to the wallet as expected
                    $wtx->wallet_id = $this->wallet_id;
                }
            } else {
                $wtx->wallet_id = $this->user->fee_wallet_id;
            }

            $wtx->num_satoshis = $networkFee;
            $wtx->ln_tx_id = NULL;
            $wtx->user_label = 'Network fee';
            $wtx->passThru = ['source_transaction'=>$this->external_hash,'fee_target_wallet'=>$this->user->feeTargetWallet];
            $wtx->wtx_type_id = WalletTransactionType::LN_NETWORK_FEE;
            if ($wtx->save()) {
                return $wtx;
            } else {
                throw new \Exception('Unable to save network fee: '.HelperComponent::getFirstErrorFromFailedValidation($wtx));
            }
        }
        return null;
    }

    public function createServiceFeeTransaction()
    {
        $rateAsDecimal = $this->user->getServiceFeeRate($this->wtx_type_id);
        if ($rateAsDecimal) {
            $serviceFee = (int) ceil(abs($this->num_satoshis)*$rateAsDecimal)*-1;
            \LNPay::info('tx: '.$this->id.' - Service fee:'.$serviceFee,__METHOD__);
            $wtx = new WalletTransaction();
            $wtx->user_id = $this->user_id;
            $wtx->wallet_id = ($this->user->feeTargetWallet==User::DATA_FEE_TARGET_WALLET_CONTAINED?$this->wallet_id:$this->user->fee_wallet_id);
            $wtx->num_satoshis = $serviceFee;
            $wtx->ln_tx_id = NULL;
            $wtx->user_label = 'LNPAY service fee ('.$this->walletTransactionType->display_name.')';
            $wtx->passThru = ['source_transaction'=>$this->external_hash,'fee_target_wallet'=>$this->user->feeTargetWallet];
            $wtx->wtx_type_id = WalletTransactionType::LN_SERVICE_FEE;
            if ($wtx->save()) {
                return $wtx;
            } else {
                throw new \Exception('Unable to save lnpay fee: '.HelperComponent::getFirstErrorFromFailedValidation($wtx));
            }
        }
        return null;
    }

    public function determineWtxType($returnActionId=false)
    {
        //what type of transaction is this?
        if (!$returnActionId) {

            //if the type is already set, use it.
            if ($this->wtx_type_id) {
                return $this->wtx_type_id;
            }

            //These are defaults, most cases the wtx_type_id will be set externally
            if ($this->num_satoshis > 0 && $this->ln_tx_id)
                return WalletTransactionType::LN_DEPOSIT;
            else if ($this->num_satoshis > 0 && !$this->ln_tx_id)
                return WalletTransactionType::LN_TRANSFER_IN;
            else if ($this->num_satoshis < 0 && !$this->ln_tx_id)
                return WalletTransactionType::LN_TRANSFER_OUT;
            else if ($this->num_satoshis < 0)
                return WalletTransactionType::LN_WITHDRAWAL;
        }

        //which action should we trigger based on the type of transaction?
        if ($returnActionId) {
            switch ($this->wtx_type_id) {
                case WalletTransactionType::LN_WITHDRAWAL:
                case WalletTransactionType::LN_LNURL_WITHDRAW:
                case WalletTransactionType::LN_LNURL_PAY_OUTBOUND:
                    $action_id = ActionName::WALLET_SEND;
                    break;
                case WalletTransactionType::LN_DEPOSIT:
                case WalletTransactionType::LN_LNURL_PAY_INBOUND:
                    $action_id = ActionName::WALLET_RECEIVE;
                    break;
                case WalletTransactionType::LN_TRANSFER_IN:
                    $action_id = ActionName::WALLET_TRANSFER_IN;
                    break;
                case WalletTransactionType::LN_TRANSFER_OUT:
                    $action_id = ActionName::WALLET_TRANSFER_OUT;
                    break;
                case WalletTransactionType::LN_LOOP_OUT:
                    $action_id = ActionName::WALLET_LOOP_OUT;
                    break;
                case WalletTransactionType::LN_LOOP_IN:
                    $action_id = ActionName::WALLET_LOOP_IN;
                    break;
                case WalletTransactionType::LN_NETWORK_FEE:
                    $action_id = ActionName::NETWORK_FEE_INCURRED;
                    break;
                default:
                    $action_id = null;
            }
            return $action_id;
        }
    }



    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->wtx_type_id = $this->determineWtxType();
                if ($this->lnTx) {
                    $lntxData = ($this->lnTx->getJsonData()?:[]);
                } else {
                    $lntxData = [];
                }

                $this->appendJsonData(ArrayHelper::merge($this->passThru,$lntxData));
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            if ($this->user->getJsonData(User::DATA_STREAMING_QUERY_TRANSACTION_BALANCE_UPDATE)) {
                $this->wallet->updateBalance($this->num_satoshis);
            } else {
                $this->wallet->updateBalance();
            }

            $this->wallet->refresh();

            if ($action = $this->determineWtxType($returnActionid=TRUE)) {
                $this->user->registerAction($action,['wtx'=>$this->toArray()]);
            }

            if (in_array($this->wtx_type_id,[WalletTransactionType::LN_WITHDRAWAL,WalletTransactionType::LN_DEPOSIT,WalletTransactionType::LN_LOOP_OUT])) {
                $this->createNetworkFeeTransaction();
                $this->createServiceFeeTransaction();
            }

        }

    }
















    /**
     *
     *
     * API STUFF
     *
     *
     */

    public function fields()
    {
        $fields = parent::fields();

        $fields['id'] = $fields['external_hash'];
        $fields['wal'] = 'wallet';

        $fields['wtxType'] = 'walletTransactionType';
        $fields['lnTx'] = function ($model) {

            if ($model->lnTx) {
                $lntx = $model->lnTx->toArray();
                unset($lntx['passThru']);
                return $lntx;
            } else {
                return null;
            }

        };

        $fields['passThru'] = function($model) {
            if ($model->json_data instanceof \yii\db\JsonExpression)
                return $model->json_data->getValue();
            else
                return $model->json_data;
        };

        // remove fields that contain sensitive information
        unset(
            $fields['user_id'],
            $fields['ln_tx_id'],
            $fields['json_data'],
            $fields['external_hash'],
            $fields['updated_at'],
            $fields['wtx_type_id'],
            $fields['wallet_id'],
            $fields['wallet_lnurlpay_id'],
            $fields['wallet_lnurlw_id']
        );

        return $fields;
    }

}
