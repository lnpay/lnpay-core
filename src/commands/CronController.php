<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lnpay\commands;

use lnpay\node\models\LnNode;
use lnpay\models\StatusType;
use lnpay\wallet\models\Wallet;
use lnpay\node\models\NodeListener;
use yii\console\Controller;

use Yii;


class CronController extends Controller
{
    public function actionMinute()
    {
        /*
        foreach (LnNode::find()->where(['status_type_id'=>StatusType::LN_NODE_ACTIVE]) as $lnNode) {
            foreach ($lnNode->nodeListeners as $nL) {
                if (!$nL->isRunning && $nL->isAutorestart) { //if node listener is not running, but should be
                    $nL->startListenerAndTurnOnAutostart();
                    echo $nL->id." Listener not running, attempting to start\n";
                }
            }
        }*/
    }

    public function actionHourly()
    {
        try {
            $this->compressLargeWallets();
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }
    }

    public function actionDaily()
    {
        /***
         * Health checks for nodes. a little finicky
         */

        /*
        try {
            foreach (LnNode::find()->where(['!=','status_type_id',StatusType::LN_NODE_INACTIVE])->all() as $lnNode) {
                $lnNode->healthCheck('REST');
                $lnNode->healthCheck('RPC');
            }
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }
        */


        /**
         * limit the growth of some tables
         */


        try {
            $this->cleanupIwhr();
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }

        try {
            $this->cleanupApiLogs();
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }

        try {
            $this->cleanupActionFeed();
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }

        try {
            $this->cleanupQueueLogs();
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }

    }

    /**
     * Delete webhook requests older than 15 days
     *
     */
    public function cleanupIwhr()
    {
        \LNPay::info('Cleaning up Webhook logs older than 3 days');
        $cutoffTime = time() - 259200; //3 days
        \LNPay::$app->db->createCommand('DELETE FROM integration_webhook_request WHERE created_at < '.$cutoffTime)->execute();
    }

    public function cleanupApiLogs()
    {
        \LNPay::info('Cleaning up API logs older than 3 days');
        $cutoffTime = time() - 259200; //3 days
        \LNPay::$app->db->createCommand('DELETE FROM user_api_log WHERE created_at < '.$cutoffTime)->execute();
    }

    public function cleanupActionFeed()
    {
        \LNPay::info('Cleaning up Action Feed older than 3 days');
        $cutoffTime = time() - 259200; //5 days
        \LNPay::$app->db->createCommand('DELETE FROM action_feed WHERE created_at < '.$cutoffTime)->execute();
    }

    public function cleanupQueueLogs()
    {
        \LNPay::info('Cleaning up Queue Push history older than 3 days');
        $cutoffTime = time() - 259200; //3 days
        \LNPay::$app->db->createCommand('DELETE FROM queue_push WHERE job_class = :class')->bindValue(':class','lnpay\jobs\AnalyticsLogJob')->execute();
        \LNPay::$app->db->createCommand('DELETE FROM queue_push WHERE pushed_at < '.$cutoffTime)->execute();

        \LNPay::info('Cleaning up Queue Exec history older than 3 days');
        $cutoffTime = time() - 259200; //3 days
        \LNPay::$app->db->createCommand('DELETE FROM queue_exec WHERE finished_at < '.$cutoffTime)->execute();
    }

    public function compressLargeWallets()
    {
        \LNPay::info('Compressing wallets with large amount of transactions');
        $walletsToCompress = [50018,28655,36202];

        foreach ($walletsToCompress as $wId) {
            $wallet = Wallet::findOne($wId);
            $wallet->compressTransactions();
        }
    }

}
