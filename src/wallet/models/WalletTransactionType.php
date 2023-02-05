<?php

namespace lnpay\wallet\models;

use Yii;

/**
 * This is the model class for table "wallet_transaction_type".
 *
 * @property int $id
 * @property string|null $layer
 * @property string $name
 * @property string $display_name
 *
 * @property WalletTransaction[] $walletTransactions
 */
class WalletTransactionType extends \yii\db\ActiveRecord
{
    const LN_DEPOSIT = 10;
    const LN_WITHDRAWAL = 20;
    const LN_TRANSFER_IN = 30;
    const LN_TRANSFER_OUT = 40;
    const LN_ROLL_UP = 45;

    const LN_LOOP_OUT = 50;
    const LN_LOOP_IN = 55;

    //@TODO: add migrations for these
    const LN_SPONTANEOUS_INBOUND = 80;
    const LN_SPONTANEOUS_OUTBOUND = 85;

    const LN_LNURL_PAY_INBOUND = 60;
    const LN_LNURL_PAY_OUTBOUND = 62;
    const LN_LNURL_WITHDRAW = 65;

    const LN_NETWORK_FEE = 70;
    const LN_SERVICE_FEE = 75;
    const LN_FEE_BALANCE_PAYMENT = 79;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_transaction_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['layer', 'name', 'display_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'layer' => 'Layer',
            'name' => 'Name',
            'display_name' => 'Display Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWalletTransactions()
    {
        return $this->hasMany(WalletTransaction::className(), ['wtx_type_id' => 'id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['id']);

        return $fields;
    }
}
