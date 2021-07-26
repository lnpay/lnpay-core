<?php

use yii\db\Migration;

/**
 * Class m200219_142429_api_version
 */
class m200219_142429_api_version extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user` ADD COLUMN `api_version` VARCHAR(255) AFTER `api_parent_id`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `user` DROP COLUMN `api_version`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200219_142429_api_version cannot be reverted.\n";

        return false;
    }
    */
}
