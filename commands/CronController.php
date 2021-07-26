<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\modules\node\models\LnNode;
use app\models\StatusType;
use yii\console\Controller;

use Yii;


class CronController extends Controller
{
    public function actionMinute()
    {

    }

    public function actionHourly()
    {

    }

    public function actionDaily()
    {
        try {
            foreach (LnNode::find()->where(['!=','status_type_id',StatusType::LN_NODE_INACTIVE])->all() as $lnNode) {
                $lnNode->healthCheck('REST');
                $lnNode->healthCheck('RPC');
            }
        } catch (\Throwable $t) {
            Yii::error($t->getMessage(),__METHOD__);
        }


        try {
            $this->cleanupIwhr();
        } catch (\Throwable $t) {
            Yii::error($t->getMessage(),__METHOD__);
        }

        try {
            $this->cleanupApiLogs();
        } catch (\Throwable $t) {
            Yii::error($t->getMessage(),__METHOD__);
        }

        try {
            $this->cleanupQueueLogs();
        } catch (\Throwable $t) {
            Yii::error($t->getMessage(),__METHOD__);
        }

    }

    /**
     * Delete webhook requests older than 15 days
     *
     */
    public function cleanupIwhr()
    {
        Yii::info('Cleaning up Webhook logs older than 15 days');
        $cutoffTime = time() - 1296000; //15 days
        Yii::$app->db->createCommand('DELETE FROM integration_webhook_request WHERE created_at < '.$cutoffTime)->execute();
    }

    public function cleanupApiLogs()
    {
        Yii::info('Cleaning up API logs older than 30 days');
        $cutoffTime = time() - 1296000*2; //30 days
        Yii::$app->db->createCommand('DELETE FROM user_api_log WHERE created_at < '.$cutoffTime)->execute();
    }

    public function cleanupQueueLogs()
    {
        Yii::info('Cleaning up Queue Push history older than 30 days');
        $cutoffTime = time() - 1296000*2; //30 days
        Yii::$app->db->createCommand('DELETE FROM queue_push WHERE job_class = :class')->bindValue(':class','app\jobs\AnalyticsLogJob')->execute();
        Yii::$app->db->createCommand('DELETE FROM queue_push WHERE pushed_at < '.$cutoffTime)->execute();

        Yii::info('Cleaning up Queue Exec history older than 30 days');
        $cutoffTime = time() - 1296000*2; //30 days
        Yii::$app->db->createCommand('DELETE FROM queue_exec WHERE finished_at < '.$cutoffTime)->execute();
    }

}
