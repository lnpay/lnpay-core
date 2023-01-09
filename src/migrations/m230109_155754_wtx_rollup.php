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
