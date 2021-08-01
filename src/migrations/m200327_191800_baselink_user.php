<?php

use yii\db\Migration;

/**
 * Class m200327_191800_baselink_user
 */
class m200327_191800_baselink_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `base_link` ADD COLUMN `user_id` INT(11) AFTER `id`");
        $this->execute("ALTER TABLE `base_link` ADD CONSTRAINT `user_id_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `base_link` DROP FOREIGN KEY `user_id_ibfk_1`");
        $this->execute("ALTER TABLE `base_link` DROP COLUMN `user_id`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_191800_baselink_user cannot be reverted.\n";

        return false;
    }
    */
}
