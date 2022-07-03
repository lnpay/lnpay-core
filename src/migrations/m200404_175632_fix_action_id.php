<?php

use yii\db\Migration;

/**
 * Class m200404_175632_fix_action_id
 */
class m200404_175632_fix_action_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `action_feed` DROP FOREIGN KEY `action_feed_ibfk_2`");
        $this->execute("ALTER TABLE `action_feed` CHANGE `action_name_id` `action_name_id` VARBINARY(255) NOT NULL");

        $this->execute("ALTER TABLE `action_name` CHANGE `id` `id` VARBINARY(255) NOT NULL");
        $this->execute("ALTER TABLE `action_feed` ADD CONSTRAINT `action_feed_ibfk_2` FOREIGN KEY (`action_name_id`) REFERENCES `action_name` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `action_feed` DROP FOREIGN KEY `action_feed_ibfk_2`");
        $this->execute("ALTER TABLE `action_feed` CHANGE `action_name_id` `action_name_id` INT(11) NOT NULL");

        $this->execute("ALTER TABLE `action_name` CHANGE `id` `id` INT(11) AUTO_INCREMENT NOT NULL");
        $this->execute("ALTER TABLE `action_feed` ADD CONSTRAINT `action_feed_ibfk_2` FOREIGN KEY (`action_name_id`) REFERENCES `action_name` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200404_175632_fix_action_id cannot be reverted.\n";

        return false;
    }
    */
}
