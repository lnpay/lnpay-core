<?php

use yii\db\Migration;

/**
 * Class m200211_113828_usr_hash
 */
class m200211_113828_usr_hash extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user` ADD COLUMN `external_hash` VARCHAR(255) AFTER `email`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `user` DROP COLUMN `external_hash`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200211_113828_usr_hash cannot be reverted.\n";

        return false;
    }
    */
}
