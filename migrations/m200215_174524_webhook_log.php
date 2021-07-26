<?php

use yii\db\Migration;

/**
 * Class m200215_174524_webhook_log
 */
class m200215_174524_webhook_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `integration_webhook_request` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                          `external_hash` varbinary(255) NOT NULL DEFAULT '',
                          `created_at` int(11) NOT NULL,
                          `integration_webhook_id` int(11) NOT NULL,
                          `action_feed_id` int(11) DEFAULT NULL,
                          `request_payload` longtext NOT NULL,
                          `response_body` longtext,
                          `response_status_code` int(11) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `integration_webhook_id` (`integration_webhook_id`),
                          KEY `external_hash` (`external_hash`),
                          KEY `action_feed_id` (`action_feed_id`),
                          CONSTRAINT `integration_webhook_request_ibfk_1` FOREIGN KEY (`integration_webhook_id`) REFERENCES `integration_webhook` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                          CONSTRAINT `integration_webhook_request_ibfk_2` FOREIGN KEY (`action_feed_id`) REFERENCES `action_feed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;");

        $this->execute("ALTER TABLE `action_name` ADD COLUMN `is_webhook` BOOL DEFAULT 0 AFTER `display_name`");
        $this->execute("UPDATE `action_name` SET `is_webhook` = 1 WHERE id IN(200,220,320,322,323,330,410,500,510,520,530,540)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `integration_webhook_request`");
        $this->execute("ALTER TABLE `action_name` DROP COLUMN `is_webhook`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200215_174524_webhook_log cannot be reverted.\n";

        return false;
    }
    */
}
