<?php

use yii\db\Migration;

/**
 * Class m200111_005210_description_hash
 */
class m200111_005210_description_hash extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `ln_tx` ADD COLUMN `description_hash` VARCHAR(255) AFTER `memo`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `ln_tx` DROP COLUMN `description_hash`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200111_005210_description_hash cannot be reverted.\n";

        return false;
    }
    */
}
