<?php

namespace lnpay\models\action;

use Yii;

/**
 * This is the model class for table "action_name".
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 */
class ActionName extends \yii\db\ActiveRecord
{
    const USER_CREATED = 400;
    const USER_PW_RESET = 405;

    const WALLET_CREATED = 500;
    const WALLET_SEND = 510;
    const WALLET_SEND_FAILURE = 515;

    const WALLET_RECEIVE = 520;
    const WALLET_TRANSFER_IN = 530;
    const WALLET_TRANSFER_OUT = 540;
    const WALLET_CHANGE_NODE = 545;

    const WALLET_LOOP_OUT = 550;
    const WALLET_LOOP_IN = 555;



    const NETWORK_FEE_INCURRED = 'billing_fee_incurred';

    const LN_NODE_USER_ADD = 605;
    const LN_NODE_USER_REMOVE = 610;
    const LN_NODE_CONNECTION_ERROR = 'ln_node_connection_error';
    const LN_NODE_SPONTANEOUS_SEND_FAILURE = 'ln_node_spontaneous_send_failure';
    const LN_NODE_INVOICE_PAYMENT_FAILURE = 'ln_node_invoice_payment_failure';

    const LND_RPC_INVOICE = 'InvoiceSubscription_Invoice';
    const LND_RPC_PEER_EVENT = 'PeerEventSubscription_PeerEvent';
    const LND_RPC_CHANNEL_EVENT_UPDATE = 'ChannelEventSubscription_ChannelEventUpdate';
    const LND_RPC_CHAN_BACKUP_SNAPSHOT = 'ChannelBackupSubscription_ChanBackupSnapshot';
    const LND_RPC_GRAPH_TOPOLOGY_UPDATE = 'GraphTopologySubscription_GraphTopologyUpdate';
    const LND_RPC_TRANSACTION = 'GetTransactionsRequest_Transaction';
    const LND_RPC_HTLC_EVENT = 'SubscribeHtlcEventsRequest_HtlcEvent';

    const TYPE_USER = 'user';
    const TYPE_WALLET = 'wallet';
    const TYPE_BILLING = 'billing';
    const TYPE_LN_NODE = 'ln_node';
    const TYPE_LND_RPC = 'lnd_rpc';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action_name';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'display_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'display_name' => 'Action',
        ];
    }

    public static function getActiveWebhookQuery()
    {
        return static::find()->where(['is_webhook'=>1])->orderBy('id DESC');
    }

    /**
     * @return array
     */
    public static function getActiveWebhookArrayByType()
    {
        $array = static::getActiveWebhookQuery()->all();

        $a = [];
        foreach ($array as $actionName) {
            $a[$actionName->type][] = ['name'=>$actionName->name,'display_name'=>$actionName->display_name];
        }

        return $a;
    }


    /**
     *
     *
     *
     *
     * API STUFF
     */

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['is_webhook']);

        return $fields;
    }
}
