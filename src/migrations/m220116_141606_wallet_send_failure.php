<?php

use yii\db\Migration;

/**
 * Class m220116_141606_wallet_send_failure
 */
class m220116_141606_wallet_send_failure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`, `is_webhook`)
                        VALUES
                            (511, 'wallet', 'wallet_send_failure', 'Wallet Send Failure', 1),
                               (512, 'wallet', 'wallet_spontaneous_send_failure', 'Wallet Outbound Keysend Failure', 1);
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('action_name',['id'=>[511,512]]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220116_141606_wallet_send_failure cannot be reverted.\n";

        return false;
    }
    */
}
