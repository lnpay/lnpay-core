<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lnpay\commands;

use lnpay\jobs\ProcessLndResponseJob;
use lnpay\models\action\ActionName;
use lnpay\node\models\LnNode;
use lnpay\node\models\NodeListener;
use yii\console\Controller;

use Yii;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RpcListenerController extends Controller
{
    public static function processLndRpcResponse($response,$nodeObject,$actionName)
    {
        $rpcData = json_decode($response->serializeToJsonString(),TRUE);
        $actionName = ActionName::findOne($actionName);

        $eventData = [
            'lnod'=> $nodeObject->toArray(),
            $actionName->name=>$rpcData
        ];

        $job = \LNPay::$app->queue->priority(100)->push(new ProcessLndResponseJob([
            'responseArray' => $rpcData,
            'nodeArray' => $nodeObject->toArray(),
            'actionArray'=>$actionName->toArray()
        ]));

        //$actionFeedObject = $nodeObject->user->registerAction($actionName->id,$eventData);
    }

    public function actionLndSubscribe($node_id,$method)
    {
        $nodeObject = LnNode::findOne($node_id);
        $connector = $nodeObject->getLndConnector();
        try {
            switch ($method) {
                case NodeListener::LND_RPC_SUBSCRIBE_HTLC_EVENTS:
                    $actionName = ActionName::LND_RPC_HTLC_EVENT;
                    $connector->rpcSubscribeHtlcEvents(
                        function (\Routerrpc\HtlcEvent $response) use ($nodeObject,$actionName) {
                            static::processLndRpcResponse($response,$nodeObject,$actionName);
                        }
                    );
                    break;
                case NodeListener::LND_RPC_SUBSCRIBE_INVOICES:
                    $actionName = ActionName::LND_RPC_INVOICE;
                    $connector->rpcSubscribeInvoices(
                        function ($response) use ($nodeObject,$actionName) {
                            static::processLndRpcResponse($response,$nodeObject,$actionName);
                        }
                    );
                    break;
                case NodeListener::LND_RPC_SUBSCRIBE_TRANSACTIONS:
                    $actionName = ActionName::LND_RPC_TRANSACTION;
                    $connector->rpcSubscribeTransactions(
                        function ($response) use ($nodeObject,$actionName) {
                            static::processLndRpcResponse($response,$nodeObject,$actionName);
                        }
                    );
                    break;
                case NodeListener::LND_RPC_SUBSCRIBE_PEER_EVENTS:
                    $actionName = ActionName::LND_RPC_PEER_EVENT;
                    $connector->rpcSubscribePeerEvents(
                        function ($response) use ($nodeObject,$actionName) {
                            static::processLndRpcResponse($response,$nodeObject,$actionName);
                        }
                    );
                    break;
                case NodeListener::LND_RPC_SUBSCRIBE_CHANNEL_EVENTS:
                    $actionName = ActionName::LND_RPC_CHANNEL_EVENT_UPDATE;
                    $connector->rpcSubscribeChannelEvents(
                        function ($response) use ($nodeObject,$actionName) {
                            static::processLndRpcResponse($response,$nodeObject,$actionName);
                        }
                    );
                    break;
                case NodeListener::LND_RPC_SUBSCRIBE_CHANNEL_GRAPH:
                    $actionName = ActionName::LND_RPC_GRAPH_TOPOLOGY_UPDATE;
                    $connector->rpcSubscribeChannelGraph(
                        function ($response) use ($nodeObject,$actionName) {
                            static::processLndRpcResponse($response,$nodeObject,$actionName);
                        }
                    );
                    break;
                case NodeListener::LND_RPC_SUBSCRIBE_CHANNEL_BACKUPS:
                    $actionName = ActionName::LND_RPC_CHAN_BACKUP_SNAPSHOT;
                    $connector->rpcSubscribeChannelBackups(
                        function ($response) use ($nodeObject,$actionName) {
                            static::processLndRpcResponse($response,$nodeObject,$actionName);
                        }
                    );
                    break;
            }

        } catch (\Throwable $t) {
            echo $t->getMessage();
            \LNPay::error($method.':'.$t, __METHOD__);
        }
    }


}
