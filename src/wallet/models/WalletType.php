<?php

namespace lnpay\wallet\models;

use Yii;

/**
 * This is the model class for table "wallet_type".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $display_name
 *
 * @property Wallet[] $wallets
 */
class WalletType extends \yii\db\ActiveRecord
{
    const GENERIC_WALLET = 5;
    const FEE_WALLET = 15;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'display_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'display_name' => 'Display Name',
        ];
    }

    /**
     * Gets query for [[Wallets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWallets()
    {
        return $this->hasMany(Wallet::className(), ['wallet_type_id' => 'id']);
    }

    public static function getAvailableWalletTypes()
    {
        return static::find()->asArray()->all();
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['id']);

        return $fields;
    }
}
