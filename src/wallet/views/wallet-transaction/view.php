<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model lnpay\wallet\models\WalletTransaction */

$this->title = $model->external_hash;
$this->params['breadcrumbs'][] = ['label' => 'Wallet Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="wallet-transaction-view">

    <div class="row">
        <div class="col-md-6">
            <div class="jumbotron">
                <h1>Wallet Transaction</h1>
                <pre><?=json_encode($model->toArray(),JSON_PRETTY_PRINT);?></pre>
            </div>
        </div>
        <?php if ($model->ln_tx_id) {?>
        <div class="col-md-6">
            <div class="jumbotron">
                <h1>LnTx Transaction</h1>
                <pre><?=json_encode($model->lnTx->toArray(),JSON_PRETTY_PRINT);?></pre>
            </div>
        </div>
        <?php } ?>
    </div>


</div>
