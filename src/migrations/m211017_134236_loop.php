<?php

use yii\db\Migration;

/**
 * Class m211017_134236_loop
 */
class m211017_134236_loop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('action_name',['id','type','name','display_name','is_webhook'],[
            [
                'id'=>550,
                'type'=>'wallet',
                'name'=>'ln_loop_out',
                'display_name'=>'Loop Out',
                'is_webhook'=>0
            ],
            [
                'id'=>555,
                'type'=>'wallet',
                'name'=>'ln_loop_in',
                'display_name'=>'Loop In',
                'is_webhook'=>0
            ]
        ]);

        $this->batchInsert('wallet_transaction_type',['id','layer','name','display_name'],[
            [
                'id'=>50,
                'layer'=>'ln',
                'name'=>'ln_loop_out',
                'display_name'=>'LN Loop Out',
            ],
            [
                'id'=>55,
                'layer'=>'ln',
                'name'=>'ln_loop_in',
                'display_name'=>'LN Loop In',
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('wallet_transaction_type',['id'=>[50,55]]);
        $this->execute("DELETE FROM `action_name` WHERE id = '50'");
        $this->execute("DELETE FROM `action_name` WHERE id = '55'");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211017_134236_loop cannot be reverted.\n";

        return false;
    }
    */
}
