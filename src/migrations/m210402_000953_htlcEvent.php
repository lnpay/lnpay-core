<?php

use yii\db\Migration;

/**
 * Class m210402_000953_htlcEvent
 */
class m210402_000953_htlcEvent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`, `is_webhook`)
                        VALUES
                            ('SubscribeHtlcEventsRequest_HtlcEvent', 'lnd_rpc', 'HtlcEvent', 'LND RPC HtlcEvent', 1);
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM action_name WHERE id IN ('SubscribeHtlcEventsRequest_HtlcEvent');");
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210402_000953_htlcEvent cannot be reverted.\n";

        return false;
    }
    */
}
