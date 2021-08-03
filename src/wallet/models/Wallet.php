<?php

namespace lnpay\wallet\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\models\LnTx;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\models\UserAccessKey;
use lnpay\wallet\models\WalletTransaction;
use lnpay\node\models\LnNode;
use Yii;
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
 * @property string $admin_key
 * @property string $invoice_key
 * @property string $readonly_key
 * @property string|null $external_hash
 *
 * @property User $user
 * @property WalletTransaction[] $walletTransactions
 */
class Wallet extends \yii\db\ActiveRecord
{
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
                    UserAccessKeyBehavior::ROLE_WALLET_LNURL_WITHDRAW
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
            [['user_id', 'balance','wallet_type_id'], 'integer'],
            [['status_type_id'],'default','value'=>StatusType::WALLET_ACTIVE],
            [['wallet_type_id'],'default','value'=>WalletType::GENERIC_WALLET],
            ['ln_node_id','default','value'=>@LnNode::getLnpayNodeQuery()->one()->id],
            ['ln_node_id','checkUserNode'],
            //[['user_label'],'unique'],
            [['user_id'],'default','value'=>function(){return \LNPay::$app->user->id;}],
            [['external_hash'],'default','value'=>function(){ return 'wal_'.HelperComponent::generateRandomString(14); }],
            [['json_data'], 'safe'],
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
            'external_hash' => 'External Hash',
            'wallet_type_id'=>'Wallet Type'
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
        if (!$this->ln_node_id) {
            return LnNode::getLnpayNodeQuery();
        }
        return $this->hasOne(LnNode::className(), ['id' => 'ln_node_id']);
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
        //if we are using the lnpay custodial node
        if ($this->ln_node_id == LnNode::getLnpayNodeQuery()->one()->id)
            return true;

        //if we are using the user's nodes, make sure they are only adding theirs
        $node = LnNode::findOne($this->ln_node_id);

        if (\LNPay::$app->user->isGuest) { //e.g. LNURL-withdraw where there is no authenticated user
            //this seems weird, but it is correct
        } else {
            if ($node->user_id != \LNPay::$app->user->id)
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

    public function updateBalance()
    {
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
    public function generateLnInvoice($invoiceOptions=[])
    {
        $lnTx = new LnTx();
        $lnTx->num_satoshis = (@$invoiceOptions['num_satoshis']?:0);
        $lnTx->memo = (@$invoiceOptions['memo']?:'Invoice: '.HelperComponent::generateRandomString(8));
        $lnTx->user_id = $this->user_id;
        $lnTx->ln_node_id = $this->ln_node_id;
        return $lnTx->generateInvoice();
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

        //$fields['passThru'] = 'json_data';

        // remove fields that contain sensitive information
        unset($fields['user_id'],$fields['ln_node_id'],$fields['external_hash'],$fields['json_data'],$fields['status_type_id'],$fields['wallet_type_id']);

        return $fields;

    }
}
