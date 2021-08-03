<?php

use lnpay\wallet\models\WalletTransaction;
use yii\db\Migration;

/**
 * Class m191215_224105_base_wallet_actions
 */
class m191215_224105_base_wallet_actions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `wallet_transaction_type` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `layer` varchar(255) DEFAULT NULL,
                      `name` varchar(255) NOT NULL DEFAULT '',
                      `display_name` varchar(255) NOT NULL DEFAULT '',
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;");

        $this->execute("INSERT INTO `wallet_transaction_type` (`id`, `layer`, `name`, `display_name`)
                        VALUES
                            (10, 'ln', 'ln_deposit', 'LN Deposit'),
                            (20, 'ln', 'ln_withdrawal', 'LN Withdrawal'),
                            (30, 'ln', 'ln_transfer_in', 'Transfer In'),
                            (40, 'ln', 'ln_transfer_out', 'Transfer Out');
                        ");

        $this->execute("ALTER TABLE `wallet_transaction` ADD COLUMN `wtx_type_id` INT(11) AFTER `wallet_id`");
        $this->execute("ALTER TABLE `wallet_transaction` ADD CONSTRAINT `wallet_transaction_ibfk_4` FOREIGN KEY (`wtx_type_id`) REFERENCES `wallet_transaction_type` (`id`)");

        $this->execute("INSERT INTO `status_type` (`id`, `type`, `name`, `display_name`)
                        VALUES
                            (200, 'wallet', 'active', 'Active'),
                            (210, 'wallet', 'inactive', 'Inactive');
                        ");

        $this->execute("ALTER TABLE `wallet` ADD COLUMN `status_type_id` INT(11) AFTER `external_hash`");
        $this->execute("ALTER TABLE `wallet` ADD CONSTRAINT `wallet_ibfk_2` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`)");

        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`)
                        VALUES
                            (500, 'wallet', 'wallet_created', 'Wallet Created'),
                            (510, 'wallet', 'wallet_send', 'Wallet Send'),
                            (520, 'wallet', 'wallet_receive', 'Wallet Receive'),
                            (530, 'wallet', 'wallet_transfer_in', 'Wallet Transfer IN'),
                            (540, 'wallet', 'wallet_transfer_out', 'Wallet Transfer OUT');
                        ");
        $this->execute("ALTER TABLE `wallet` AUTO_INCREMENT = 2500");
        $this->execute("ALTER TABLE `user_access_key` AUTO_INCREMENT = 1000");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `wallet_transaction` DROP FOREIGN KEY `wallet_transaction_ibfk_4`");
        $this->execute("ALTER TABLE `wallet_transaction` DROP COLUMN `wtx_type_id`");
        $this->execute("DROP TABLE `wallet_transaction_type`");

        $this->execute("DELETE FROM `wallet` WHERE `user_label` = 'DEFAULT WALLET'");
        $this->execute("DELETE FROM `wallet_transaction` WHERE `user_label` IN('Balance transfer from legacy wallet','Legacy Withdraw')");
        $this->execute("ALTER TABLE `wallet` DROP FOREIGN KEY `wallet_ibfk_2`");
        $this->execute("ALTER TABLE `wallet` DROP COLUMN `status_type_id`");
        $this->execute("DELETE FROM `action_feed` WHERE `action_name_id` IN (500,510,520,530,540)");
        $this->execute("DELETE FROM `action_name` WHERE `id` IN (500,510,520,530,540)");

        $this->execute("DELETE FROM `status_type` WHERE `id` IN (200,210)");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191215_224105_base_wallet_actions cannot be reverted.\n";

        return false;
    }
    */
}
