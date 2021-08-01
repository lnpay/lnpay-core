<?php

use yii\db\Migration;

/**
 * Class m200128_005039_api_logger
 */
class m200126_005039_api_logger extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `user_api_log` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                          `user_id` int(11) DEFAULT NULL,
                          `api_key` varchar(255) DEFAULT NULL,
                          `request_path` text DEFAULT NULL,
                          `request_body` longtext,
                          `request_headers` longtext,
                          `response_body` longtext,
                          `response_headers` longtext,
                          PRIMARY KEY (`id`),
                          KEY `user_id` (`user_id`),
                          CONSTRAINT `user_api_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `user_api_log`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200128_005039_api_logger cannot be reverted.\n";

        return false;
    }
    */
}
