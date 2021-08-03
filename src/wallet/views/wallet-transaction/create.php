<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\wallet\models\WalletTransaction */

$this->title = 'Create Wallet Transaction';
$this->params['breadcrumbs'][] = ['label' => 'Wallet Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallet-transaction-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
