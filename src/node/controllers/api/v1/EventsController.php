<?php

namespace lnpay\node\controllers\api\v1;

use lnpay\base\ApiController;
use lnpay\components\HelperComponent;
use lnpay\node\models\analytics\HtlcEventQueryForm;
use lnpay\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class EventsController extends ApiController
{
    public $nodeObject;
    public $modelClass = 'lnpay\node\models\LnNode';

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['index']);
        return $actions;
    }

    protected function verbs(){
        return [
            //'create' => ['POST'],
            //'update' => ['PUT','PATCH','POST'],
            //'delete' => ['DELETE'],
            //'view' =>   ['GET','OPTIONS'],
            //'index'=>   ['GET'],
        ];
    }

    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {
            if ($node_id = \LNPay::$app->request->getQueryParam('node_id')) {
                $this->nodeObject = LnNode::find()->where(['id'=>$node_id,'user_id'=>\LNPay::$app->user->id])->one();

                if (!$this->nodeObject) {
                    throw new UnauthorizedHttpException('Invalid node id: '.$node_id);
                }
            } else {
                throw new UnauthorizedHttpException('Request must contain node_id');
            }
        }

        return true;
    }

    public function actionHtlc()
    {
        $model = new HtlcEventQueryForm();
        $model->load(\LNPay::$app->request->getQueryParams(),'');

        if ($model->validate()) {
            //execute query
            $query = $model->constructQuery();
            return $query->all();
        } else {
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($model));
        }
    }

    public function actionHtlcsummary($period) //minute, hour, day
    {
        $model = new HtlcEventQueryForm();
        $model->load(\LNPay::$app->request->getQueryParams(),'');

        if (!$model->endAt)
            $model->endAt = time()*1000000000;

        $arr = [];


        if ($model->validate()) {
            //execute query
            $query = $model->constructQuery();
            $results = $query->all();



            foreach ($results as $r) {
                $d = new \DateTime();
                $ts = $r['timestampNs']/1000000000;
                $d->setTimestamp($ts);
                $d->setTimezone(new \DateTimeZone('America/New_York'));
                //echo $d->format('H:i:s').'<br/>';
                if (!@$r['outgoingChannelId']) {
                    continue;
                }
                switch ($period) {
                    case 'second':
                        $t = strtotime($d->format('Y-m-d H:i:s'));
                        if (empty($arr[$r['outgoingChannelId'][$t]]))
                            $arr[$r['outgoingChannelId']][$t] = 0;
                            $arr[$r['outgoingChannelId']][$t]++;
                        break;
                    case 'minute':
                        $t = strtotime($d->format('Y-m-d H:i:00'));
                        if (empty($arr[$r['outgoingChannelId']][$t]))
                            $arr[$r['outgoingChannelId']][$t] = 0;
                            $arr[$r['outgoingChannelId']][$t]++;
                        break;
                    case 'hour':
                        $t = strtotime($d->format('Y-m-d H:00:00'));
                        if (empty($arr[$r['outgoingChannelId']][$t]))
                            $arr[$r['outgoingChannelId']][$t] = 0;
                            $arr[$r['outgoingChannelId']][$t]++;
                        break;
                }
            }

            $jsf=[];

            foreach ($arr as $channelId => $vars) {
                foreach ($vars as $timestamp => $count) {
                    if (empty($jsf[(string) $timestamp])) {
                        $jsf[(string) $timestamp] = [$channelId=>$count];
                    }
                    else {
                       @$jsf[(string) $timestamp][$channelId]+=$count;
                    }
                    $jsf[(string) $timestamp]['time'] = $timestamp;
                }
            }
            return array_values($jsf);


        } else {
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($model));
        }
    }




}
