<?php
namespace lnpay\components;

use lnpay\jobs\AnalyticsLogJob;
use Yii;
use yii\base\BaseObject;
use yii\base\Component;


class AnalyticsComponent extends Component
{
    public static function log($userId,$eventName,$params=[])
    {
        if (getenv('AMPLITUDE_API_KEY')) {
            AnalyticsComponent::executeLog($userId,$eventName,$params);
            /*
            \LNPay::$app->queue->priority(2048)->push(new AnalyticsLogJob([
                'userId' => $userId,
                'eventName' => $eventName,
                'params'=>$params
            ]));
            */
        }
    }

    public static function executeLog($userId,$eventName,$params=[])
    {
        $excludeUsers = [2476];
        if (in_array($userId,$excludeUsers))
            return true;

        if (getenv('AMPLITUDE_API_KEY')) {
            if (!YII_ENV_TEST) {
                $amplitude = new \Zumba\Amplitude\Amplitude();
                $amplitude->init(getenv('AMPLITUDE_API_KEY'), $userId);
                $amplitude->logEvent($eventName,$params);
            }
        }
    }

}
