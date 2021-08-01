<?php

use yii\db\Migration;

/**
 * Class m200208_211205_api_log_changes
 */
class m200208_211205_api_log_changes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user_api_log` ADD COLUMN `status_code` INT(11) AFTER `request_headers`");
        $this->execute("ALTER TABLE `user_api_log` ADD COLUMN `sdk` VARCHAR(255) AFTER `api_key`");
        $this->execute("ALTER TABLE `user_api_log` ADD COLUMN `external_hash` VARCHAR(255) AFTER `user_id`");
        $this->execute("ALTER TABLE `user_api_log` ADD COLUMN `ip_address` VARCHAR(255) AFTER `api_key`");
        $this->execute("ALTER TABLE `user_api_log` ADD COLUMN `base_url` VARCHAR(255) AFTER `method`");
        $this->execute("ALTER TABLE `user_api_log` ADD COLUMN `created_at` INT(11) AFTER `id`");
        $this->execute("ALTER TABLE `action_feed` ADD COLUMN `external_hash` VARCHAR(255) AFTER `created_at`");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `user_api_log` DROP COLUMN `status_code`");
        $this->execute("ALTER TABLE `user_api_log` DROP COLUMN `sdk`");
        $this->execute("ALTER TABLE `user_api_log` DROP COLUMN `external_hash`");
        $this->execute("ALTER TABLE `user_api_log` DROP COLUMN `ip_address`");
        $this->execute("ALTER TABLE `user_api_log` DROP COLUMN `base_url`");
        $this->execute("ALTER TABLE `user_api_log` DROP COLUMN `created_at`");

        $this->execute("ALTER TABLE `action_feed` DROP COLUMN `external_hash`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200208_211205_api_log_changes cannot be reverted.\n";

        return false;
    }
    */
}
