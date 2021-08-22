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
            [['eventType','node_id'],'required'],
            [['incomingChannelId','outgoingChannelId','startAt','endAt'],'integer'],
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

        if ($this->startAt) {
            $query->andWhere(['>','timestampNs',$this->startAt]);
        }

        if ($this->endAt) {
            $query->andWhere(['<','timestampNs',$this->endAt]);
        }

        $query->from($this->node_id);

        return $query;
    }
}
