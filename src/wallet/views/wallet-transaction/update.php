<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\wallet\models\WalletTransaction */

$this->title = 'Update Wallet Transaction: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wallet Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="wallet-transaction-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
