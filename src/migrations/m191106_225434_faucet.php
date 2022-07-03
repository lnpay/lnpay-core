<?php

use yii\db\Migration;

/**
 * Class m191106_185434_faucet
 */
class m191106_225434_faucet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `base_link` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `created_at` int(11) DEFAULT NULL,
                      `updated_at` int(11) DEFAULT NULL,
                      `rep` int(11) DEFAULT NULL,
                      `short_url` varchar(255) NOT NULL DEFAULT '',
                      `destination_url` varchar(255) DEFAULT NULL,
                      `custy_domain_id` int(11) NOT NULL,
                      `status_type_id` int(11) NOT NULL,
                      `json_data` longtext,
                      PRIMARY KEY (`id`),
                      KEY `custy_domain_id` (`custy_domain_id`),
                      KEY `status_type_id` (`status_type_id`),
                      CONSTRAINT `base_link_ibfk_1` FOREIGN KEY (`custy_domain_id`) REFERENCES `custy_domain` (`id`),
                      CONSTRAINT `base_link_ibfk_2` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `base_link_analytics` (
                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                      `created_at` int(11) DEFAULT NULL,
                      `updated_at` int(11) DEFAULT NULL,
                      `base_link_id` int(11) NOT NULL,
                      `engagement_type` varchar(255) NOT NULL DEFAULT '',
                      `domain` varchar(255) DEFAULT NULL,
                      `referrer` varchar(255) DEFAULT NULL,
                      `requester_ip` varchar(255) DEFAULT NULL,
                      `device_type` varchar(11) DEFAULT NULL,
                      `json_data` longtext,
                      PRIMARY KEY (`id`),
                      KEY `base_link_id` (`base_link_id`),
                      CONSTRAINT `base_link_analytics_ibfk_1` FOREIGN KEY (`base_link_id`) REFERENCES `base_link` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `ln_tx` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `created_at` int(11) NOT NULL,
                      `updated_at` int(11) NOT NULL,
                      `user_id` int(11) DEFAULT NULL,
                      `dest_pubkey` varchar(255) NOT NULL DEFAULT '',
                      `payment_request` varchar(1024) NOT NULL DEFAULT '',
                      `r_hash_decoded` varchar(255) DEFAULT NULL,
                      `memo` varchar(255) DEFAULT NULL,
                      `num_satoshis` int(11) NOT NULL,
                      `expiry` int(11) DEFAULT NULL,
                      `expires_at` int(11) DEFAULT NULL,
                      `payment_preimage` varchar(255) DEFAULT NULL,
                      `settled` int(11) NOT NULL DEFAULT '0',
                      `settled_at` int(11) DEFAULT NULL,
                      `json_data` longtext,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `payment_request` (`payment_request`),
                      KEY `created_at` (`created_at`),
                      KEY `settled_at` (`settled_at`),
                      CONSTRAINT `ln_tx_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `log_console` (
                      `id` bigint(20) NOT NULL AUTO_INCREMENT,
                      `level` int(11) DEFAULT NULL,
                      `category` varchar(255) DEFAULT NULL,
                      `log_time` int(11) DEFAULT NULL,
                      `prefix` text,
                      `message` text,
                      PRIMARY KEY (`id`),
                      KEY `idx_log_level` (`level`),
                      KEY `idx_log_category` (`category`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='1';");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `integration_webhook` DROP FOREIGN KEY `integration_webhook_ibfk_6`");
        $this->execute("ALTER TABLE `integration_webhook` DROP `faucet_id`");

        $this->execute("DROP TABLE `base_link_analytics`");
        $this->execute("DROP TABLE `base_link`");

        $this->execute("DROP TABLE `ln_tx`");

        $this->execute("DROP TABLE `log_console`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191106_185434_faucet cannot be reverted.\n";

        return false;
    }
    */
}
