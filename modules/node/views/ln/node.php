<?php

use app\models\LnNodeSearch;
use app\modules\node\models\NodeListener;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LnNodeSearch */
/* @var $node \app\modules\node\models\LnNode */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'LN Node Dashboard';
$this->params['breadcrumbs'][] = 'Info';

$activeChannels = $node->getInfoValueByKey('num_active_channels');
$onchain_active = $node->onchain_total_sats > 0;
$nodeReady = $activeChannels && $onchain_active && $gi;
?>

<?php /* ?>
    <div class="pull-right">
        <h3>Status: <?=$node->statusType->display_name;?></h3>
        <?php if ($node->status_type_id == \app\models\StatusType::LN_SUBNODE_STOPPED) echo Html::a('Start Node',['/node/ln/start-node'],['class'=>'btn btn-success']); ?>
        <?php if ($node->status_type_id == \app\models\StatusType::LN_SUBNODE_RUNNING) echo Html::a('Stop Node',['/node/ln/stop-node'],['class'=>'btn btn-danger']); ?>
    </div>
<?php */ ?>
<div class="jumbotron well">
    <h2>Node Status <i class="glyphicon glyphicon-<?=$nodeReady?'ok':'remove';?>"></i></h2>
    <p>
    <ul class="list-group">
        <li class="list-group-item">1. Node is connected! <i class="glyphicon glyphicon-<?=$gi?'ok':'remove';?>"></i></li>
        <li class="list-group-item">2. <a href="/node/ln/onchain">Deposit 0.01 BTC on-chain</a> <i class="glyphicon glyphicon-<?=$onchain_active?'ok':'remove';?>"></i></li>
        <li class="list-group-item">3. Can send/receive on the Lightning Network <i class="glyphicon glyphicon-<?=$activeChannels?'ok':'remove';?>"></i></li>
        <li class="list-group-item"></li>
    </ul>
    </p>
</div>
<?php
if (Yii::$app->request->getQueryParam('showinfo')) {
    echo \yii\bootstrap\Tabs::widget([
        'items' => [
            [
                'label' => 'Info',
                'content' => $this->render('_node-details', compact('node')),
                'active' => true
            ],
            /*[
                'label' => 'Macaroons',
                'content' => 'Anim pariatur cliche...',
                'headerOptions' => [],
                'options' => ['id' => 'myveryownID'],
            ],
            [
                'label' => 'RPC Listeners',
                'content' => $this->render('../rpc/rpc-listeners',compact('dataProvider'))
            ],*/
        ],
    ]);

    echo Html::a('Refresh Getinfo (REST)', ['/node/ln/test-call'], ['class' => 'btn btn-primary']);
}
?>



