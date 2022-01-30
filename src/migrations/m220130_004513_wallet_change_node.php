<?php

use yii\db\Migration;

/**
 * Class m220130_004513_wallet_change_node
 */
class m220130_004513_wallet_change_node extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`, `is_webhook`)
                        VALUES
                               (545, 'wallet', 'wallet_change_node', 'Wallet Change Node', 1)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('action_name',['id'=>['545']]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220130_004513_wallet_change_node cannot be reverted.\n";

        return false;
    }
    */
}
