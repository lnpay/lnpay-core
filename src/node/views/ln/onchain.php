<?php

use lnpay\models\LnNodeSearch;
use lnpay\node\models\NodeListener;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $balances [] */
/* @var $node \lnpay\node\models\LnNode */

$this->title = 'On-chain';
$this->params['breadcrumbs'][] = 'Info';
?>


<div class="jumbotron well">
    <p>

    <ul class="list-group">
        <li class="list-group-item">Total On-chain Balance: <?=$node->onchain_total_sats/100000000;?> BTC</li>
        <li class="list-group-item">Unconfirmed On-chain Balance: <?=$node->onchain_unconfirmed_sats;?> BTC</li>
        <li class="list-group-item">Confirmed On-chain Balance: <?=$node->onchain_confirmed_sats/100000000;?> BTC</li>
    </ul>
    </p>

    <h2>Deposit onchain:</h2>
    <img src="/distro-router/qr?str=<?=$node->onchain_nextaddr;?>&a=<?=$node->id;?>">
    <div class="well"><?=$node->onchain_nextaddr;?></div>

</div>
<?=Html::a('Refresh Balances',['/node/ln/test-call'],['class'=>'btn btn-primary']); ?>

