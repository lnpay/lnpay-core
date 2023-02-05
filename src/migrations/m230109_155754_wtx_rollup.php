<?php

use yii\db\Migration;

/**
 * Class m230109_155754_wtx_rollup
 */
class m230109_155754_wtx_rollup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('wallet_transaction_type',[
            'id'=>45,
            'layer'=>'ln',
            'name'=>'balance_roll_up',
            'display_name'=>'Balance Roll Up'
        ]);

        //fix this so we can delete wallets. this prevents deletion due to FK
        $this->dropForeignKey('wallet_ibfk_5','wallet');
        $this->addForeignKey('wallet_ibfk_5','wallet','default_lnurlpay_id','wallet_lnurlpay','id','SET NULL','SET NULL');

        $this->dropForeignKey('wallet_ibfk_6','wallet');
        $this->addForeignKey('wallet_ibfk_6','wallet','default_lnurlw_id','wallet_lnurlw','id','SET NULL','SET NULL');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('wallet_transaction_type',['id'=>45]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230109_155754_wtx_rollup cannot be reverted.\n";

        return false;
    }
    */
}
