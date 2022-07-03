<?php

use yii\db\Migration;

/**
 * Class m200311_160034_non_custodial
 */
class m200318_160034_non_custodial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `ln_node_implementation` (
                          `name` varchar(64) NOT NULL DEFAULT '',
                          `display_name` varchar(255) DEFAULT NULL,
                          `json_data` json DEFAULT NULL,
                          PRIMARY KEY (`name`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("INSERT INTO `ln_node_implementation` (`name`, `display_name`, `json_data`)
                        VALUES
                            ('lnd', 'LND', NULL);
                        ");

        $this->execute("CREATE TABLE `ln_node` (
                      `id` varbinary(255) NOT NULL DEFAULT '',
                      `created_at` int(11) NULL,
                      `updated_at` int(11) NULL,
                      `user_id` int(11) NOT NULL,
                      `alias` varchar(255) DEFAULT NULL,
                      `network` varchar(255) NOT NULL,
                      `ln_node_implementation_id` varchar(64) NOT NULL DEFAULT '',
                      `default_pubkey` varchar(255) DEFAULT NULL,
                      `wallet_password` varchar(255) DEFAULT NULL,
                      `uri` varchar(255) DEFAULT NULL,
                      `host` varchar(255) DEFAULT '',
                      `rpc_port` int(11) NOT NULL,
                      `rest_port` int(11) NOT NULL,
                      `ln_port` int(11) NOT NULL,
                      `tls_cert` text,
                      `getinfo` json DEFAULT NULL,
                      `status_type_id` int(11) DEFAULT NULL,
                      `rpc_status_id` int(11) DEFAULT NULL,
                      `rest_status_id` int(11) DEFAULT NULL,
                      `internal_rpc_port` int(11) DEFAULT NULL,
                      `internal_rest_port` int(11) DEFAULT NULL,
                      `internal_ln_port` int(11) DEFAULT NULL,
                      `json_data` json DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `ln_node_implementation_id` (`ln_node_implementation_id`),
                      KEY `status_type_id` (`status_type_id`),
                      KEY `rpc_status_id` (`rpc_status_id`),
                      KEY `rest_status_id` (`rest_status_id`),
                      KEY `user_id` (`user_id`),
                      CONSTRAINT `ln_node_ibfk_1` FOREIGN KEY (`ln_node_implementation_id`) REFERENCES `ln_node_implementation` (`name`),
                      CONSTRAINT `ln_node_ibfk_2` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`),
                      CONSTRAINT `ln_node_ibfk_3` FOREIGN KEY (`rpc_status_id`) REFERENCES `status_type` (`id`),
                      CONSTRAINT `ln_node_ibfk_4` FOREIGN KEY (`rest_status_id`) REFERENCES `status_type` (`id`),
                      CONSTRAINT `ln_node_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("ALTER TABLE `ln_node` ADD COLUMN `fee_wallet_id` INT(11)");
        $this->execute("ALTER TABLE `ln_node` ADD CONSTRAINT `ln_node_ibfk_6` FOREIGN KEY (`fee_wallet_id`) REFERENCES `wallet` (`id`)");

        $this->execute("ALTER TABLE `ln_node` ADD COLUMN `keysend_wallet_id` INT(11)");
        $this->execute("ALTER TABLE `ln_node` ADD CONSTRAINT `ln_node_ibfk_7` FOREIGN KEY (`keysend_wallet_id`) REFERENCES `wallet` (`id`)");


        $this->execute("CREATE TABLE `ln_node_profile` (
                  `id` varbinary(255) NOT NULL DEFAULT '',
                  `created_at` int(11) NULL,
                  `updated_at` int(11) NULL,
                  `user_id` int(11) NOT NULL,
                  `ln_node_id` varbinary(255) NOT NULL DEFAULT '',
                  `is_default` tinyint(1) DEFAULT NULL,
                  `user_label` varchar(255) NOT NULL DEFAULT '',
                  `status_type_id` int(11) DEFAULT NULL,
                  `macaroon_hex` text,
                  `username` varchar(255) DEFAULT NULL,
                  `password` varchar(255) DEFAULT NULL,
                  `access_key` varchar(255) DEFAULT NULL,
                  `json_data` json DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `ln_node_id` (`ln_node_id`),
                  KEY `user_id` (`user_id`),
                  KEY `status_type_id` (`status_type_id`),
                  CONSTRAINT `ln_node_profile_ibfk_1` FOREIGN KEY (`ln_node_id`) REFERENCES `ln_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                  CONSTRAINT `ln_node_profile_ibfk_2` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`),
                  CONSTRAINT `ln_node_profile_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("INSERT INTO `status_type` (`id`, `type`, `name`, `display_name`)
                        VALUES
                            (300, 'ln_node', 'active', 'Active'),
                            (305, 'ln_node', 'inactive', 'Inactive'),
                            (309, 'ln_node', 'error', 'Error Connecting'),
                            (320, 'ln_node_rpc', 'up', 'Up'),
                            (325, 'ln_node_rpc', 'inactive', 'Inactive'),
                            (329, 'ln_node_rpc', 'error', 'Error Connecting'),
                            (330, 'ln_node_rest', 'up', 'Up'),
                            (335, 'ln_node_rest', 'inactive', 'Inactive'),
                            (339, 'ln_node_rest', 'error', 'Error Connecting'),
                            (400, 'ln_node_profile', 'active', 'Active'),
                            (405, 'ln_node_profile', 'inactive', 'Inactive')
                               ;
                        ");

        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`, `is_webhook`)
                        VALUES
                            (605, 'ln_node', 'user_node_add', 'User added LN node', 1),
                            (610, 'ln_node', 'user_node_remove', 'User removed LN node', 1)
                            ;
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `ln_node_profile`");
        $this->execute("DROP TABLE `ln_node`");
        $this->execute("DROP TABLE `ln_node_implementation`");
        $this->execute("DELETE FROM `status_type` WHERE `type` IN ('ln_node_rest','ln_node_rpc','ln_node','ln_node_profile')");
        $this->execute("DELETE FROM `action_feed` WHERE `action_name_id` IN (605,610)");
        $this->execute("DELETE FROM `action_name` WHERE `type` IN ('ln_node')");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200311_160034_non_custodial cannot be reverted.\n";

        return false;
    }
    */
}
