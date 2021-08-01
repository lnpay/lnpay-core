<?php

use yii\db\Migration;

/**
 * Class m191214_120829_minor_updates
 */
class m191214_120829_minor_updates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `ln_tx` ADD COLUMN `external_hash` VARCHAR(255) AFTER `user_id`, ADD INDEX (external_hash)");
        $this->execute("ALTER TABLE `base_link_analytics` ADD COLUMN `user_agent` TEXT AFTER `requester_ip`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `base_link_analytics` DROP COLUMN `user_agent`");
        $this->execute("ALTER TABLE `ln_tx` DROP COLUMN `external_hash`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191214_120829_minor_updates cannot be reverted.\n";

        return false;
    }
    */
}
