<?php

use yii\db\Migration;

/**
 * Class m191124_113412_tz
 */
class m191124_113412_tz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `user` ADD COLUMN `tz` VARCHAR(255) DEFAULT NULL AFTER `balance`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `user` DROP `tz`");
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191124_113412_tz cannot be reverted.\n";

        return false;
    }
    */
}
