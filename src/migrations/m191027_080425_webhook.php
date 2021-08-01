<?php

use yii\db\Migration;

/**
 * Class m191027_080425_webhook
 */
class m191027_080425_webhook extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `integration_service` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) NOT NULL DEFAULT '',
                          `display_name` varchar(255) NOT NULL DEFAULT '',
                          `json_data` longtext,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB AUTO_INCREMENT=311 DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `integration_webhook` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `user_id` int(11) NOT NULL,
                          `action_name_id` int(11) NOT NULL,
                          `integration_service_id` int(11) NOT NULL,
                          `http_method` varchar(255) DEFAULT NULL,
                          `endpoint_url` varchar(255) DEFAULT NULL,
                          `json_data` longtext,
                          `created_at` int(11) DEFAULT NULL,
                          `updated_at` int(11) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `action_name_id` (`action_name_id`),
                          KEY `user_id` (`user_id`),
                          KEY `integration_service_id` (`integration_service_id`),
                          CONSTRAINT `integration_webhook_ibfk_2` FOREIGN KEY (`action_name_id`) REFERENCES `action_name` (`id`),
                          CONSTRAINT `integration_webhook_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
                          CONSTRAINT `integration_webhook_ibfk_5` FOREIGN KEY (`integration_service_id`) REFERENCES `integration_service` (`id`)
                        ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");

        $this->execute("INSERT INTO `integration_service` (`id`, `name`, `display_name`, `json_data`)
                        VALUES
                            (300, 'zapier', 'Zapier', NULL),
                            (310, 'ifttt', 'IFTTT', NULL);
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `integration_webhook`");
        $this->execute("DROP TABLE `integration_service`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191027_080425_webhook cannot be reverted.\n";

        return false;
    }
    */
}
