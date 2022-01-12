<?php

namespace app\wallet\models;

use lnpay\behaviors\JsonDataBehavior;
use Yii;

/**
 * This is the model class for table "wallet_lnurlw".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 * @property int $wallet_id
 * @property int $status_type_id
 * @property string $external_hash
 * @property string|null $json_data
 * @property string|null $lnurl_encoded
 * @property string|null $lnurl_decoded
 * @property int|null $lnurlw_minWithdrawable_msat
 * @property int|null $lnurlw_maxWithdrawable_msat
 * @property string|null $lnurlw_defaultDescription
 *
 * @property User $user
 * @property Wallet $wallet
 * @property StatusType $statusType
 */
class WalletLnurlw extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_lnurlw';
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
            [['id',  'user_id', 'wallet_id', 'status_type_id', 'external_hash'], 'required'],
            [['id',  'user_id', 'wallet_id', 'status_type_id', 'lnurlw_minWithdrawable_msat', 'lnurlw_maxWithdrawable_msat'], 'integer'],
            [['json_data'], 'safe'],
            [['lnurl_encoded', 'lnurl_decoded', 'lnurlw_defaultDescription'], 'string'],
            [['external_hash'], 'string', 'max' => 45],
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
            'status_type_id' => 'Status Type ID',
            'external_hash' => 'External Hash',
            'json_data' => 'Json Data',
            'lnurl_encoded' => 'Lnurl Encoded',
            'lnurl_decoded' => 'Lnurl Decoded',
            'lnurlw_minWithdrawable_msat' => 'Lnurlw Min Withdrawable Msat',
            'lnurlw_maxWithdrawable_msat' => 'Lnurlw Max Withdrawable Msat',
            'lnurlw_defaultDescription' => 'Lnurlw Default Description',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Wallet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWallet()
    {
        return $this->hasOne(Wallet::className(), ['id' => 'wallet_id']);
    }

    /**
     * Gets query for [[StatusType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatusType()
    {
        return $this->hasOne(StatusType::className(), ['id' => 'status_type_id']);
    }
}
