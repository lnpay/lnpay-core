<?php

use yii\db\Migration;

/**
 * Class m200406_132737_lnd_rpc_action_names
 */
class m200406_132737_lnd_rpc_action_names extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('action_name',['id'=>\lnpay\models\action\ActionName::LND_RPC_INVOICE,'name'=>'Invoice'],['id'=>'10000']);
        $this->update('action_name',['id'=>\lnpay\models\action\ActionName::LND_RPC_PEER_EVENT,'name'=>'PeerEvent'],['id'=>'10001']);
        $this->update('action_name',['id'=>\lnpay\models\action\ActionName::LND_RPC_CHANNEL_EVENT_UPDATE,'name'=>'ChannelEventUpdate'],['id'=>'10002']);
        $this->update('action_name',['id'=>\lnpay\models\action\ActionName::LND_RPC_CHAN_BACKUP_SNAPSHOT,'name'=>'ChanBackupSnapshot'],['id'=>'10003']);
        $this->update('action_name',['id'=>\lnpay\models\action\ActionName::LND_RPC_GRAPH_TOPOLOGY_UPDATE,'name'=>'GraphTopologyUpdate'],['id'=>'10004']);
        $this->update('action_name',['id'=>\lnpay\models\action\ActionName::LND_RPC_TRANSACTION,'name'=>'Transaction'],['id'=>'10005']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update('action_name',['id'=>10000,'name'=>'InvoiceSubscription_Invoice'],['id'=>\lnpay\models\action\ActionName::LND_RPC_INVOICE]);
        $this->update('action_name',['id'=>10001,'name'=>'PeerEventSubscription_PeerEvent'],['id'=>\lnpay\models\action\ActionName::LND_RPC_PEER_EVENT]);
        $this->update('action_name',['id'=>10002,'name'=>'ChannelEventSubscription_ChannelEventUpdate'],['id'=>\lnpay\models\action\ActionName::LND_RPC_CHANNEL_EVENT_UPDATE]);
        $this->update('action_name',['id'=>10003,'name'=>'ChannelBackupSubscription_ChanBackupSnapshot'],['id'=>\lnpay\models\action\ActionName::LND_RPC_CHAN_BACKUP_SNAPSHOT]);
        $this->update('action_name',['id'=>10004,'name'=>'GraphTopologySubscription_GraphTopologyUpdate'],['id'=>\lnpay\models\action\ActionName::LND_RPC_GRAPH_TOPOLOGY_UPDATE]);
        $this->update('action_name',['id'=>10005,'name'=>'GetTransactionsRequest_Transaction'],['id'=>\lnpay\models\action\ActionName::LND_RPC_TRANSACTION]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200406_132737_lnd_rpc_action_names cannot be reverted.\n";

        return false;
    }
    */
}
