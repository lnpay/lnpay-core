<?php

use yii\db\Migration;

/**
 * Class m220124_143632_lnurlpay_inoutbound
 */
class m220124_143632_lnurlpay_inoutbound extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('wallet_transaction_type',[
            'id'=>62,
            'layer'=>'ln',
            'name'=>'ln_lnurl_pay_outbound',
            'display_name'=>'LNURL Pay Outbound',
        ]);

        $this->update('wallet_transaction_type',
            ['name'=>'ln_lnurl_pay_inbound'],
            ['id'=>\lnpay\wallet\models\WalletTransactionType::LN_LNURL_PAY_INBOUND]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('wallet_transaction_type',['id'=>62]);
        $this->update('wallet_transaction_type',
            ['name'=>'ln_lnurl_pay'],
            ['id'=>\lnpay\wallet\models\WalletTransactionType::LN_LNURL_PAY_INBOUND]
        );

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220124_143632_lnurlpay_inoutbound cannot be reverted.\n";

        return false;
    }
    */
}
