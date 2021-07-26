<?php

use yii\db\Migration;

/**
 * Class m191024_120848_action
 */
class m191024_120848_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `action_name` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) NOT NULL DEFAULT '',
                          `display_name` varchar(255) NOT NULL DEFAULT '',
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB AUTO_INCREMENT=411 DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `action_feed` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `created_at` int(11) DEFAULT NULL,
                          `action_name_id` int(11) DEFAULT NULL,
                          `user_id` int(11) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `user_id` (`user_id`),
                          CONSTRAINT `action_feed_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                          CONSTRAINT `action_feed_ibfk_2` FOREIGN KEY (`action_name_id`) REFERENCES `action_name` (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `action_data` (
                          `action_feed_id` int(11) NOT NULL,
                          `data` longtext,
                          PRIMARY KEY (`action_feed_id`),
                          CONSTRAINT `action_data_ibfk_1` FOREIGN KEY (`action_feed_id`) REFERENCES `action_feed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `action_data`");
        $this->execute("DROP TABLE `action_feed`");
        $this->execute("DROP TABLE `action_name`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191024_120848_action cannot be reverted.\n";

        return false;
    }
    */
}
