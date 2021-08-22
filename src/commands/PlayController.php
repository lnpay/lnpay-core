<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lnpay\commands;

use lnpay\node\models\LnNode;
use lnpay\models\StatusType;
use yii\console\Controller;

use Yii;
use yii\helpers\VarDumper;


class PlayController extends Controller
{
    public function actionMongo()
    {
        $collection = Yii::$app->mongodb->getCollection('lnod_djti8zrfrwk3cx');
        $collection->insert(json_decode('{"eventType": "FORWARD", "timestampNs": "1629599419839150984", "linkFailEvent": {"info": {"incomingAmtMsat": "209121872", "outgoingAmtMsat": "209120663", "incomingTimelock": 697362, "outgoingTimelock": 697322}, "wireFailure": "TEMPORARY_CHANNEL_FAILURE", "failureDetail": "INSUFFICIENT_BALANCE", "failureString": "insufficient bandwidth to route htlc"}, "incomingHtlcId": "24887", "incomingChannelId": "123456789", "outgoingChannelId": "762928028804382735"}',true));

        /*
        $query = new \yii\mongodb\Query();
        $rows = $query->select(['name','bedrooms'])
            ->where(['bedrooms'=>3])
            ->from('listingsAndReviews')->limit(10)->all();
        echo VarDumper::export($rows);
        */
    }

    public function actionPopulate()
    {


    }

}
