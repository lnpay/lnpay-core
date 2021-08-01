<?php

use lnpay\models\LnNodeSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\models\LnNodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPC Listeners';
$this->params['breadcrumbs'][] = $this->title;


?>
<h1>RPC Listeners</h1>
    <p>
        Starting a RPC Listener initiates the server -> client stream of data from the node. These events can be forwarded
        to an HTTP endpoint. The point of this is to save you the hassle of maintaining your own RPC listeners.
    </p>
    <div class="ln-node-index">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                [
                    'header'=>'Status',
                    'format'=>'raw',
                    'value'=>function($model) {
                        $info = $model->supervisorProcessInfo;
                        if ($info['statename']=='RUNNING')
                            return '<i class="fas fa-check fa-2x" style="color:green;"></i>';
                        else
                            return '<i class="fas fa-minus fa-2x" style="color:gray;"></i>';
                    }
                ],
                'method',
                'updated_at:datetime',
                [
                    'header'=>'Node',
                    'value'=>'lnNode.alias'
                ],
                //'is_default',

                //'status_type_id',
                //'macaroon_hex:ntext',
                //'username',
                //'password',
                //'access_key',
                //'json_data',

                [
                        'header'=>'Action',
                    'format'=>'raw',
                    'value'=>function($model) {
                        $info = $model->supervisorProcessInfo;
                        switch ($info['statename']) {
                            case 'RUNNING':
                                //STOP
                                return \yii\helpers\Html::a('Stop Listener',['/node/rpc/listeners','action'=>'stop','nl_id'=>$model->id],['class'=>'btn btn-danger']);
                                break;
                            case 'STOPPED':
                            case 'EXITED':
                            case 'FATAL':
                            case null:
                                //START
                                return \yii\helpers\Html::a('Start Listener',['/node/rpc/listeners','action'=>'start','nl_id'=>$model->id],['class'=>'btn btn-success']);
                                break;
                            case 'STOPPING':
                            case 'STARTING':
                            case 'BACKOFF':
                                //CHILL
                                break;
                        }
                    }
                ]
            ],
        ]); ?>

    <a href="/node/rpc/refresh-listeners" class="btn btn-danger" onClick="return confirm('DO NOT DO THIS');">Destroy and Create listeners</a>
    </div>

