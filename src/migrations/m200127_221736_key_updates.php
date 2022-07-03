<?php

use lnpay\components\HelperComponent;
use lnpay\models\User;
use lnpay\models\UserAccessKey;
use yii\db\Migration;

/**
 * Class m200127_221736_key_updates
 */
class m200127_221736_key_updates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //Add new wallet_tx_read
        $this->execute("INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`)
                        VALUES
                            ('wallet_tx_read', 2, 'Wallet Tx Read', NULL, NULL, 1580216401, 1580216401);
                        ");

        //Remove wallet_read from Wallet Invoice role
        $this->execute("DELETE FROM `auth_item_child` WHERE `parent` = 'Wallet Invoice' AND `child` = 'wallet_read'");

        //Add wallet_tx_read to Wallet Invoice role
        $this->execute("INSERT INTO `auth_item_child` (`parent`, `child`)
                        VALUES
                            ('Wallet Invoice', 'wallet_tx_read');
                        ");

        //Add wallet_tx_read to Wallet Read role
        $this->execute("INSERT INTO `auth_item_child` (`parent`, `child`)
                        VALUES
                            ('Wallet Read', 'wallet_tx_read');
                        ");

        //Add wallet_tx_read to Wallet Admin role
        $this->execute("INSERT INTO `auth_item_child` (`parent`, `child`)
                        VALUES
                            ('Wallet Admin', 'wallet_tx_read');
                        ");


        $this->execute("ALTER TABLE `user_access_key` ADD COLUMN `status_type_id` INT(11) AFTER `access_key`");
        $this->execute("ALTER TABLE `user_access_key` ADD CONSTRAINT `user_access_key_ibfk_3` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`)");

        $this->execute("INSERT INTO `status_type` (`id`, `type`, `name`, `display_name`)
                        VALUES
                            (30, 'user_access_key', 'active', 'Active'),
                            (35, 'user_access_key', 'inactive', 'Inactive');
                        ");

        $this->execute("UPDATE `user_access_key` SET `status_type_id` = 30");

        //Create new roles
        $this->execute("INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`)
                        VALUES
                            ('Public API Key', 1, 'Identifies account, basic permission', NULL, NULL, 1580216401, 1580216401),
                            ('Secret API Key', 1, 'Can perform all actions', NULL, NULL, 1580216401, 1580216401);
                        ");

        $this->execute("ALTER TABLE `user_access_key` MODIFY `wallet_id` INT(11)");

        $this->execute("ALTER TABLE `user_api_log` ADD COLUMN `method` VARCHAR(255) AFTER `api_key`");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM `auth_item_child` WHERE `child` = 'wallet_tx_read'");
        $this->execute("INSERT INTO `auth_item_child` (`parent`, `child`)
                        VALUES
                            ('Wallet Invoice', 'wallet_read');
                        ");
        $this->execute("DELETE FROM `auth_item` WHERE `name` IN ('wallet_tx_read','Public API Key','Secret API Key')");

        $this->execute("ALTER TABLE `user_access_key` DROP FOREIGN KEY `user_access_key_ibfk_3`");
        $this->execute("ALTER TABLE `user_access_key` DROP COLUMN `status_type_id`");
        $this->execute("DELETE FROM `status_type` WHERE `id` IN (30,35)");
        $this->execute("DELETE FROM `user_access_key` WHERE `wallet_id` IS NULL");
        $this->execute("ALTER TABLE `user_access_key` MODIFY `wallet_id` INT(11) NOT NULL");
        $this->execute("ALTER TABLE `user_api_log` DROP COLUMN `method`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200127_221736_key_updates cannot be reverted.\n";

        return false;
    }
    */
}
