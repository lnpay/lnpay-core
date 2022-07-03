<?php

use yii\db\Migration;

/**
 * Class m210707_194325_node_onchain
 */
class m210707_194325_node_onchain extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `ln_node` ADD COLUMN `onchain_confirmed_sats` INT(11) DEFAULT 0 AFTER `alias`");
        $this->execute("ALTER TABLE `ln_node` ADD COLUMN `onchain_unconfirmed_sats` INT(11) DEFAULT 0 AFTER `onchain_confirmed_sats`");
        $this->execute("ALTER TABLE `ln_node` ADD COLUMN `onchain_total_sats` INT(11) DEFAULT 0 AFTER `onchain_unconfirmed_sats`");
        $this->execute("ALTER TABLE `ln_node` ADD COLUMN `onchain_nextaddr` VARCHAR(255) AFTER `onchain_total_sats`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `ln_node` DROP COLUMN `onchain_confirmed_sats`");
        $this->execute("ALTER TABLE `ln_node` DROP COLUMN `onchain_unconfirmed_sats`");
        $this->execute("ALTER TABLE `ln_node` DROP COLUMN `onchain_total_sats`");
        $this->execute("ALTER TABLE `ln_node` DROP COLUMN `onchain_nextaddr`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210707_194325_node_onchain cannot be reverted.\n";

        return false;
    }
    */
}
