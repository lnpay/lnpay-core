<?php

namespace lnpay\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\User;
use lnpay\wallet\models\Wallet;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "wallet_user_key".
 *
 * @property int $id
 * @property int $user_id
 * @property int $wallet_id
 * @property string $api_key
 * @property string|null $json_data
 *
 * @property User $user
 * @property Wallet $wallet
 */
class UserAccessKey extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_access_key';
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
            [['user_id','access_key'], 'required'],
            [['user_id', 'wallet_id'], 'integer'],
            [['status_type_id'],'default','value'=>StatusType::UAK_ACTIVE],
            [['json_data'], 'safe'],
            [['access_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'wallet_id' => 'Wallet ID',
            'access_key' => 'Api Key',
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

    public static function createKey($user_id,$role,$attributes)
    {
        $auth = \LNPay::$app->authManager;

        $prefix = HelperComponent::getRolePrefix($role);
        $apiKey = HelperComponent::generateKeyByRolePrefix($prefix);

        $wuk = new UserAccessKey();
        $wuk->attributes = $attributes;
        $wuk->user_id = $user_id;;
        $wuk->access_key = $apiKey;
        if (!$wuk->save())
            throw new ServerErrorHttpException('Cannot save API keys for wallet!');

        $auth->assign($auth->getRole($role),$wuk->access_key);
        return $wuk;
    }
}
