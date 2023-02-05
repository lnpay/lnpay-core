<?php

use lnpay\models\LnNodeSearch;
use lnpay\node\models\NodeListener;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\models\LnNodeSearch */
/* @var $node \lnpay\node\models\LnNode */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'LN Node Dashboard';
$this->params['breadcrumbs'][] = 'Dashboard';

$activeChannels = $node->getInfoValueByKey('num_active_channels');
$onchain_active = $node->onchain_total_sats > 0;
$nodeReady = $activeChannels && $onchain_active && $gi;
?>

<div class="row mb-3">
    <div class="col-xl-3 col-sm-6 py-2">
        <div class="card bg-info text-white h-100">
            <div class="card-body bg-info">
                <div class="rotate">
                    <i class="fa fa-arrow-right fa-4x"></i>
                </div>
                <h6 class="text-uppercase">Max Send</h6>
                <h1 class="display-6"><?=number_format(@$gi['max_send']) ?? 0;?> Sats</h1>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 py-2">
        <div class="card text-white bg-primary h-100">
            <div class="card-body bg-primary">
                <div class="rotate">
                    <i class="fa fa-arrow-left fa-4x"></i>
                </div>
                <h6 class="text-uppercase">Max Receive</h6>
                <h1 class="display-6"><?=number_format(@$gi['max_receive']) ?? 0;?> Sats</h1>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 py-2">
        <div class="card text-white bg-warning h-100">
            <div class="card-body bg-warning">
                <div class="rotate">
                    <i class="fa fa-twitter fa-4x"></i>
                </div>
                <h6 class="text-uppercase">On-Chain Balance</h6>
                <h1 class="display-6"> <?=number_format($node->onchain_total_sats) ?? 0;?> Sats</h1>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 py-2">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <div class="rotate">
                    <i class="fa fa-dollar-sign fa-4x"></i>
                </div>
                <h6 class="text-uppercase">Lightning Balance</h6>
                <h1 class="display-6"> <?=number_format(@$gi['balances']['localBalance']['sat']) ?? 0;?> Sats</h1>
            </div>
        </div>
    </div>
</div>
<?php
    echo \yii\bootstrap4\Tabs::widget([
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

?>



