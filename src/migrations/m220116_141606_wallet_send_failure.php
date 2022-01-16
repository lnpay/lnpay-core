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
                               (515, 'wallet', 'wallet_send_failure', 'Wallet Send Failure', 1),
                            ('ln_node_invoice_payment_failure', 'ln_node', 'ln_node_invoice_payment_failure', 'LN Node Send Failure', 1),
                               ('ln_node_spontaneous_send_failure', 'ln_node', 'ln_node_spontaneous_send_failure', 'LN Node Keysend Failure', 1);
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('action_name',['id'=>['ln_node_invoice_payment_failure','ln_node_spontaneous_send_failure','515']]);

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
