<?php

use yii\db\Migration;

/**
 * Class m200112_221805_webhook_mods
 */
class m200112_221805_webhook_mods extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `integration_webhook` ADD COLUMN `content_type` VARCHAR(255) AFTER `http_method`");
        $this->execute("ALTER TABLE `integration_webhook` ADD COLUMN `secret` VARCHAR(255) AFTER `integration_service_id`");
        $this->execute("ALTER TABLE `integration_webhook` ADD COLUMN `status_type_id` INT(11) AFTER `endpoint_url`");
        $this->execute("ALTER TABLE `integration_webhook` ADD CONSTRAINT `integration_webhook_ibfk_7` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`)");

        $this->execute("INSERT INTO `status_type` (`id`, `type`, `name`, `display_name`)
                            VALUES
                                (230, 'webhook', 'active', 'Active'),
                                (235, 'webhook', 'inactive', 'Inactive');
                            ");

        $this->execute("INSERT INTO `integration_service` (`id`, `name`, `display_name`, `json_data`)
                            VALUES
                                (500, 'user_service', 'User Service', NULL);
                            ");

        $this->execute("UPDATE `integration_webhook` SET `status_type_id` = 230");

        $this->execute("ALTER TABLE `integration_webhook` DROP FOREIGN KEY `integration_webhook_ibfk_2`");
        $this->execute("DROP INDEX `action_name_id` ON `integration_webhook`");
        $this->execute("ALTER TABLE `integration_webhook` MODIFY `action_name_id` JSON");

        $this->execute("ALTER TABLE `integration_webhook` ADD COLUMN `external_hash` VARCHAR(255) NOT NULL AFTER `id`");
        $this->execute("ALTER TABLE `integration_webhook` ADD INDEX `external_hash` (`external_hash`)");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `integration_webhook` DROP FOREIGN KEY `integration_webhook_ibfk_7`");
        $this->execute("DELETE FROM `status_type` WHERE `id` IN (230,235)");
        $this->execute("TRUNCATE `integration_webhook`");
        $this->execute("DELETE FROM `integration_service` WHERE `id` IN (500)");
        $this->execute("ALTER TABLE `integration_webhook` DROP COLUMN `content_type`");
        $this->execute("ALTER TABLE `integration_webhook` DROP COLUMN `external_hash`");
        $this->execute("ALTER TABLE `integration_webhook` DROP COLUMN `secret`");
        $this->execute("ALTER TABLE `integration_webhook` DROP COLUMN `status_type_id`");
        $this->execute("ALTER TABLE `integration_webhook` MODIFY `action_name_id` INT(11)");

        $this->execute("ALTER TABLE `integration_webhook` ADD INDEX `action_name_id` (`action_name_id`)");
        $this->execute("ALTER TABLE `integration_webhook` ADD CONSTRAINT `integration_webhook_ibfk_2` FOREIGN KEY (`action_name_id`) REFERENCES `action_name` (`id`)");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200112_221805_webhook_mods cannot be reverted.\n";

        return false;
    }
    */
}
