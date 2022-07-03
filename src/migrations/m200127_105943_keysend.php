<?php

use yii\db\Migration;

/**
 * Class m200127_105943_keysend
 */
class m200127_105943_keysend extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `ln_tx` ADD COLUMN `is_keysend` TINYINT(1) AFTER `settled_at`");
        $this->execute("ALTER TABLE `ln_tx` ADD COLUMN `custom_records` JSON AFTER `is_keysend`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `ln_tx` DROP COLUMN `is_keysend`");
        $this->execute("ALTER TABLE `ln_tx` DROP COLUMN `custom_records`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200127_105943_keysend cannot be reverted.\n";

        return false;
    }
    */
}
