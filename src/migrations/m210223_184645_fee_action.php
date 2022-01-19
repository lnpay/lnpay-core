<?php

use yii\db\Migration;

/**
 * Class m210223_184645_fee_action
 */
class m210223_184645_fee_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`, `is_webhook`)
                        VALUES
                            ('billing_fee_incurred', 'network_fee', 'network_fee_incurred', 'LN Network Fee Incurred', 1)
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM action_name WHERE id IN ('network_fee_incurred','billing_fee_payment');");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210223_184645_fee_action cannot be reverted.\n";

        return false;
    }
    */
}
