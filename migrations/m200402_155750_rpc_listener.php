<?php

use yii\db\Migration;

/**
 * Class m200402_155750_rpc_listener
 */
class m200402_155750_rpc_listener extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `node_listener` (
                          `id` varbinary(255) NOT NULL DEFAULT '',
                          `method` varchar(255) DEFAULT NULL,
                          `created_at` int(11) DEFAULT NULL,
                          `updated_at` int(11) DEFAULT NULL,
                          `ln_node_id` varbinary(255) DEFAULT NULL,
                          `btc_node_id` int(11) DEFAULT NULL,
                          `user_id` int(11) NOT NULL,
                          `config_filename` varchar(255) NOT NULL DEFAULT '',
                          `status_type_id` int(11) DEFAULT NULL,
                          `supervisor_parameters` json DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `user_id` (`user_id`),
                          KEY `status_type_id` (`status_type_id`),
                          KEY `ln_node_id` (`ln_node_id`),
                          KEY `config_filename` (`config_filename`),
                          CONSTRAINT `node_listener_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                          CONSTRAINT `node_listener_ibfk_3` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`),
                          CONSTRAINT `node_listener_ibfk_4` FOREIGN KEY (`ln_node_id`) REFERENCES `ln_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`, `is_webhook`)
                            VALUES
                                (10000, 'lnd_rpc', 'InvoiceSubscription_Invoice', 'LND RPC Invoice', 1),
                                (10001, 'lnd_rpc', 'PeerEventSubscription_PeerEvent', 'LND RPC PeerEvent', 1),
                                (10002, 'lnd_rpc', 'ChannelEventSubscription_ChannelEventUpdate', 'LND RPC ChannelEventUpdate', 1),
                                (10003, 'lnd_rpc', 'ChannelBackupSubscription_ChanBackupSnapshot', 'LND RPC ChanBackupSnapshot', 1),
                                (10004, 'lnd_rpc', 'GraphTopologySubscription_GraphTopologyUpdate', 'LND RPC GraphTopologyUpdate', 1),
                                (10005, 'lnd_rpc', 'GetTransactionsRequest_Transaction', 'LND RPC Transaction', 1);
                            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('node_listener');
        $this->execute("DELETE FROM `action_name` WHERE `type` = 'lnd_rpc'");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200402_155750_rpc_listener cannot be reverted.\n";

        return false;
    }
    */
}
