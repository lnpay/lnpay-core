<?php

namespace lnpay\wallet\models;

use lnpay\wallet\models\WalletLnurlpay;
use lnpay\behaviors\JsonDataBehavior;
use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\models\LnTx;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\models\UserAccessKey;
use lnpay\wallet\exceptions\UnableToGenerateLnurlpayException;
use lnpay\wallet\models\WalletTransaction;
use lnpay\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "wallet".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 * @property string|null $user_label
 * @property int $balance
 * @property int|null $ln_node_id
 * @property string|null $json_data
 * @property int|null $default_lnurlpay_id
 * @property int|null $default_lnurlw_id
 * @property string|null $external_hash
 *
 * @property User $user
 * @property WalletTransaction[] $walletTransactions
 */
class Wallet extends \yii\db\ActiveRecord
{
    public $deterministic_identifier = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
            JsonDataBehavior::class,
            [
                'class'=>UserAccessKeyBehavior::class,
                'default_roles'=>[
                    UserAccessKeyBehavior::ROLE_WALLET_ADMIN,
                    UserAccessKeyBehavior::ROLE_WALLET_INVOICE,
                    UserAccessKeyBehavior::ROLE_WALLET_READ,
                    UserAccessKeyBehavior::ROLE_WALLET_LNURL_WITHDRAW,
                    UserAccessKeyBehavior::ROLE_WALLET_LNURL_PAY,
                    UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_ADMIN,
                    UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_VIEW,
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_label'], 'required'],
            [['user_id', 'balance','wallet_type_id','default_lnurlpay_id','default_lnurlw_id'], 'integer'],
            [['status_type_id'],'default','value'=>StatusType::WALLET_ACTIVE],
            [['wallet_type_id'],'default','value'=>WalletType::GENERIC_WALLET],
            ['ln_node_id','default','value'=>@LnNode::getCustodialNodeQuery(\LNPay::$app->user->id)->one()->id],
            ['ln_node_id','checkUserNode'],
            [['user_id'],'default','value'=>function(){return \LNPay::$app->user->id;}],
            [['external_hash'],'default','value'=>function(){ return 'wal_'.HelperComponent::generateRandomString(14); }],
            [['external_hash'],'unique'],
            [['json_data','deterministic_identifier'], 'safe'],
            [['user_label', 'external_hash', 'ln_node_id'], 'string', 'max' => 255]
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
            'user_label' => 'Internal Label',
            'balance' => 'Balance',
            'ln_node_id' => 'LN Node ID',
            'json_data' => 'Json Data',
            'admin_key' => 'Admin Key',
            'invoice_key' => 'Invoice Key',
            'readonly_key' => 'Readonly Key',
            'external_hash' => 'External Identifier',
            'wallet_type_id'=>'Wallet Type',
            'default_lnurlpay_id'=>'Default LNURL PAY',
            'default_lnurlw_id'=>'Default LNURL WITHDRAW'
        ];
    }

    public static function findByHash($external_hash)
    {
        return static::find()->where(['external_hash'=>$external_hash])->one();
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
    public function getStatus()
    {
        return $this->hasOne(StatusType::className(), ['id' => 'status_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWalletType()
    {
        return $this->hasOne(WalletType::className(), ['id' => 'wallet_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLnNode()
    {
        return $this->hasOne(LnNode::className(), ['id' => 'ln_node_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultWalletLnurlpay()
    {
        return $this->hasOne(WalletLnurlpay::className(), ['id' => 'default_lnurlpay_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWalletTransactions()
    {
        return $this->hasMany(WalletTransaction::className(), ['wallet_id' => 'id']);
    }

    public static function findByKey($key)
    {
        $w = UserAccessKey::find()->where(['access_key'=>$key])->one();
        if ($w)
            return static::findOne($w->wallet_id);
        else
            return null;
    }

    public static function findById($id)
    {
        return static::find()->where(['external_hash'=>$id])->one();
    }

    public function checkUserNode($attribute,$params)
    {
        //if we are using the user's nodes, make sure they are only adding theirs
        $node = LnNode::findOne($this->ln_node_id);
        $user_id = (\LNPay::$app instanceof \yii\web\Application?\LNPay::$app->user->id:$this->user_id);

        if (\LNPay::$app instanceof \yii\web\Application && \LNPay::$app->user->isGuest) { //e.g. LNURL-withdraw where there is no authenticated user
            //this seems weird, but it is correct
        } else {
            $user = User::findOne($user_id);

            //if we are using the org custodial node
            $userNodes = $user->getLnNodeQuery()->all();
            foreach ($userNodes as $uN) { //if submitted node id matches one we already have
                if ($uN->user_id == $node->user_id)
                    return true;
            }

            if ($node->user_id != $user_id)
                $this->addError('ln_node_id','Node does not belong to this user!');
        }

    }

    public function getPublicId()
    {
        return $this->external_hash;
    }

    public function calculateBalance()
    {
        $sum = \LNPay::$app->db->createCommand(
            'SELECT SUM(num_satoshis) 
             FROM wallet_transaction 
             WHERE wallet_id = '.$this->id
        )->queryScalar();

        return $sum ?? 0;
    }

    public function updateBalance($num_satoshis=NULL)
    {
        //if amount is specified, debit/credit directly from balance, skipping transaction scan-sum
        if ($num_satoshis) {
            try {
                \Yii::$app->db->transaction(function() use ($num_satoshis) {
                    \Yii::$app->db
                        ->createCommand("UPDATE wallet SET balance=balance+{$num_satoshis} WHERE id={$this->id}")
                        ->execute();
                });
                return true;
            } catch (\Throwable $t) {
                \LNPay::error($t->getMessage(),__METHOD__);
            }
        }

        $this->balance = $this->calculateBalance();
        if ($this->save(false))
            return true;
        else
            throw new \yii\web\ServerErrorHttpException('Unable to update balance!');
    }

    public function getAvailableBalance()
    {
        return $this->balance;
    }

    /*
     * Delete all wallet transactions and replace with 1 debit and 1 credit transactions
     */
    public function compressTransactions()
    {
        Yii::info('compressing wallet id: '.$this->id);
        $sumQuery = WalletTransaction::find()
            ->where(['wallet_id'=>$this->id]);

        $sum = (int)$sumQuery->sum('num_satoshis');

        WalletTransaction::deleteALl(['wallet_id'=>$this->id]);

        $newDebitRow = new WalletTransaction();
        $newDebitRow->user_id = $this->user_id;
        $newDebitRow->wallet_id = $this->id;
        $newDebitRow->num_satoshis = $sum;
        $newDebitRow->ln_tx_id = NULL;
        $newDebitRow->user_label = 'Balance roll up '.date('Y-m-d h:i:s');
        $newDebitRow->wtx_type_id = WalletTransactionType::LN_ROLL_UP;
        $newDebitRow->save();
        Yii::info("(Wallet: {$this->id}) compress debit/credit row ID:{$newDebitRow->id}");

        $this->updateBalance();

        return ['balance'=>$this->balance,'sum'=>$sum];

    }

    /**
     * @param int $sats
     * @return bool
     */
    public function getIsEligibleToWithdraw($sats=null)
    {
        if ($sats == 0 || !$sats)
            return false;
        if ($this->balance <= 0)
            return false;
        else if ($this->balance < $sats)
            return false;
        else
            return true;
    }

    /**
     * @param array $invoiceOptions
     * @return LnTx
     * @throws ServerErrorHttpException
     */
    public function generateLnInvoice($invoiceOptions=[],$passThru=[])
    {
        $lnTx = new LnTx();
        $lnTx->num_satoshis = (@$invoiceOptions['num_satoshis']?:0);
        $lnTx->description_hash = @$invoiceOptions['description_hash'];
        $lnTx->memo = (@$invoiceOptions['memo']?:'Invoice: '.HelperComponent::generateRandomString(8));
        $lnTx->user_id = $this->user_id;
        $lnTx->ln_node_id = $this->ln_node_id;
        $lnTx->passThru = $passThru;
        $lnTx->appendJsonData(['wallet_id'=>$this->external_hash]);
        if ($lnTx->validate())
            $lnTxObject = $lnTx->generateInvoice($checkLimits=true);
        else
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($lnTx));
        return $lnTxObject;
    }

    /**
     * @param array $lnurlp_data
     * @return WalletLnurlpay
     */
    public function generateLnurlpay($lnurlp_data=[],$metadata=[])
    {
        $lnurlpModel = WalletLnurlpay::generateNewModel($lnurlp_data);

        $lnurlpModel->user_id = $this->user_id;
        $lnurlpModel->wallet_id = $this->id;

        if ($lnurlpModel->save()) {
            return $lnurlpModel;
        } else {
            throw new UnableToGenerateLnurlpayException(HelperComponent::getFirstErrorFromFailedValidation($lnurlpModel));
        }
    }

    public function payLnInvoice($request,$options)
    {
        return $this->lnNode->getLndConnector()->payInvoice($request,$options);
    }

    public function getLnurlWithdrawLinkEncoded($access_key=null,$params=[])
    {
        if (!$access_key)
            $access_key = $this->getFirstAccessKeyByRole(UserAccessKeyBehavior::ROLE_WALLET_LNURL_WITHDRAW);

        if (isset($params['ott']))
            $this->appendJsonData(['ott'=>[$params['ott']=>$params['ott']]]);

        return \tkijewski\lnurl\encodeUrl(\LNPay::$app->urlManager->createAbsoluteUrl(["/v1/wallet/{$access_key}/lnurl-process",'tag'=>'withdraw','ott'=>@$params['ott'],'num_satoshis'=>@$params['num_satoshis'],'memo'=>@$params['memo'],'passThru'=>@$params['passThru']]));
    }

    public function getIsFeeWallet()
    {
        return LnNode::find()->where(['fee_wallet_id'=>$this->id])->exists();
    }

    public function getIsKeysendWallet()
    {
        return LnNode::find()->where(['keysend_wallet_id'=>$this->id])->exists();
    }

    public function releaseMutex()
    {
        \LNPay::$app->mutex->release($this->publicId);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {

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
            //Generate default LNURL links
            $lnurlp_data = [
                'user_label'=>"LNURL-pay ".$this->user_label,
                'lnurlp_short_desc'=>$this->user_label,
            ];

            if ($this->wallet_type_id == WalletType::FEE_WALLET) {
                $lnurlp_data['lnurlp_maxSendable_msat'] = 100000000; //100,000 sat
            }

            $l = $this->generateLnurlpay($lnurlp_data);
            $this->default_lnurlpay_id = $l->id;
            $this->save();

            $this->refresh();
            $this->user->registerAction(ActionName::WALLET_CREATED,['wal'=>$this->toArray()]);
        }
    }












    /**
     *
     *
     * API RESPONSE FIELDS
     *
     *
     */

    public function fields()
    {
        $fields = parent::fields();
        $fields['id'] = $fields['external_hash'];
        $fields['statusType'] = 'status';
        $fields['walletType'] = 'walletType';
        $fields['default_lnurlpay_id'] = function ($model) {
            if ($model->default_lnurlpay_id)
                return $model->defaultWalletLnurlpay->external_hash;
            else
                return null;
        };
        //$fields['passThru'] = 'json_data';

        // remove fields that contain sensitive information
        unset($fields['user_id'],
            $fields['external_hash'],
            $fields['json_data'],
            $fields['status_type_id'],
            $fields['wallet_type_id'],
            $fields['default_lnurlw_id']
        );

        return $fields;

    }
}
