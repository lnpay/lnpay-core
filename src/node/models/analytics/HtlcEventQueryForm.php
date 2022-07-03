<?php
namespace lnpay\node\models\analytics;

use lnpay\components\HelperComponent;
use lnpay\models\StatusType;
use yii\base\Model;

use Yii;
use yii\helpers\VarDumper;

/**
 * Node Add Form
 */
class HtlcEventQueryForm extends Model
{
    public $node_id;

    public $eventType;
    public $incomingChannelId;
    public $outgoingChannelId;
    public $startAt;
    public $endAt;

    public $linkFailEvent;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['node_id'],'required'],
            [['incomingChannelId','outgoingChannelId'],'integer'],
            [['endAt'],'default','value'=>function ($model,$attribute) { return time(); }],
            [['eventType'],'string'],
            [['linkFailEvent'],'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [

        ];
    }

    public function constructQuery()
    {
        $query = new \yii\mongodb\Query();
        $query
            ->andFilterWhere(['eventType'=>$this->eventType])
            ->andFilterWhere(['incomingChannelId'=>$this->incomingChannelId])
            ->andFilterWhere(['outgoingChannelId'=>$this->outgoingChannelId]);

        $query->from($this->node_id.'_SubscribeHtlcEventsRequest_HtlcEvent');

        return $query;
    }
}
