<?php

use yii\db\Migration;

/**
 * Class m191210_233454_wallet
 */
class m191210_233454_wallet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->baseWalletPerms();
        $this->execute("CREATE TABLE `wallet` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `created_at` int(11) NOT NULL,
                      `updated_at` int(11) NOT NULL,
                      `user_id` int(11) NOT NULL,
                      `user_label` varchar(255) DEFAULT NULL,
                      `balance` int(11) NOT NULL DEFAULT '0',
                      `node_id` int(11) DEFAULT NULL,
                      `external_hash` varchar(255) NOT NULL DEFAULT '',
                      `json_data` json DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `wallet_transaction` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `created_at` int(11) NOT NULL,
                  `updated_at` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `wallet_id` int(11) NOT NULL,
                  `num_satoshis` int(11) NOT NULL,
                  `ln_tx_id` int(11) DEFAULT NULL,
                  `user_label` varchar(255) DEFAULT NULL,
                  `external_hash` varchar(255) NOT NULL,
                  `json_data` json DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `wallet_id` (`wallet_id`),
                  KEY `ln_tx_id` (`ln_tx_id`),
                  CONSTRAINT `wallet_transaction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                  CONSTRAINT `wallet_transaction_ibfk_2` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                  CONSTRAINT `wallet_transaction_ibfk_3` FOREIGN KEY (`ln_tx_id`) REFERENCES `ln_tx` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `user_access_key` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `created_at` int(11) NOT NULL,
                      `updated_at` int(11) NOT NULL,
                      `user_id` int(11) NOT NULL,
                      `wallet_id` int(11) NOT NULL,
                      `access_key` varchar(255) NOT NULL DEFAULT '',
                      `json_data` json DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `key` (`access_key`),
                      KEY `user_id` (`user_id`),
                      KEY `wallet_id` (`wallet_id`),
                      CONSTRAINT `wallet_user_key_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      CONSTRAINT `wallet_user_key_ibfk_2` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function baseWalletPerms()
    {
        $auth = \LNPay::$app->authManager;

        // add "createPost" permission
        $read = $auth->createPermission('wallet_read');
        $read->description = 'Wallet Read';
        $auth->add($read);

        // add "createPost" permission
        $deposit = $auth->createPermission('wallet_deposit');
        $deposit->description = 'Wallet Deposit';
        $auth->add($deposit);

        // add "createPost" permission
        $withdraw = $auth->createPermission('wallet_withdraw');
        $withdraw->description = 'Wallet Withdraw';
        $auth->add($withdraw);

        // add "createPost" permission
        $transfer = $auth->createPermission('wallet_transfer');
        $transfer->description = 'Wallet Transfer';
        $auth->add($transfer);

        // add "author" role and give this role the "createPost" permission
        $role = $auth->createRole('Wallet Admin');
        $role->description = 'Can read,deposit,transfer,withdraw';
        $auth->add($role);
        $auth->addChild($role, $read);
        $auth->addChild($role, $deposit);
        $auth->addChild($role, $withdraw);
        $auth->addChild($role, $transfer);

        // add "author" role and give this role the "createPost" permission
        $role = $auth->createRole('Wallet Read');
        $role->description = 'Can read';
        $auth->add($role);
        $auth->addChild($role, $read);

        // add "author" role and give this role the "createPost" permission
        $role = $auth->createRole('Wallet Invoice');
        $role->description = 'Can read,deposit';
        $auth->add($role);
        $auth->addChild($role, $read);
        $auth->addChild($role, $deposit);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = \LNPay::$app->authManager;
        $auth->removeAll();
        $this->execute("DROP TABLE `wallet_transaction`");
        $this->execute("DROP TABLE `user_access_key`");
        $this->execute("DROP TABLE `wallet`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191210_233454_wallet cannot be reverted.\n";

        return false;
    }
    */
}
